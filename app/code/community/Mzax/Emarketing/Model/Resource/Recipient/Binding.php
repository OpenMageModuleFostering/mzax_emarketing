<?php
/**
 * Mzax Emarketing (www.mzax.de)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this Extension in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mzax_Emarketing_Model_Resource_Recipient_Binding
 */
class Mzax_Emarketing_Model_Resource_Recipient_Binding
{
    /**
     * @var Zend_Db_Select
     */
    protected $_direct;

    /**
     * @var array
     */
    protected $_bindings = array();

    /**
     * @var array
     */
    protected $_unionSelects = array();

    /**
     * @var
     */
    protected $_campaignFilter;

    /**
     * @var
     */
    protected $_variationFilter;

    /**
     * @var string[]
     */
    protected $_columns = array('recipient_id');

    /**
     * @var array
     */
    protected $_where = array();

    /**
     * @param $column
     *
     * @return $this
     */
    public function setColumns($column)
    {
        $this->_columns = (array)$column;

        return $this;
    }

    /**
     * Retrieve select object for current binding
     *
     * @param boolean $wrap
     *
     * @return Zend_Db_Select
     */
    public function getSelect($wrap = true)
    {
        $unions = array();
        if ($this->_direct) {
            $unions[] = $this->_direct;
        }

        $unions = array_merge($unions, $this->_unionSelects);

        if (empty($unions)) {
            return null;
        }

        $final = array();
        foreach ($unions as $select) {
            if ($this->_prepareSelect($select)) {
                $final[] = $select;
            }
        }

        if (count($final) === 1) {
            $select = $final[0];
        } else {
            $select = $this->getResourceHelper()->getWriteAdapter()->select();
            $select->union($final, Zend_Db_Select::SQL_UNION);
        }

        if ($wrap) {
            $wrapper = $this->getResourceHelper()->getWriteAdapter()->select();
            $wrapper->from(array('binder' => $select), '*');
            return $wrapper;
        }

        return $select;
    }

    /**
     * @param $campaign
     *
     * @return $this
     */
    public function addCampaignFilter($campaign)
    {
        if ($campaign instanceof Varien_Object) {
            $campaign = $campaign->getId();
        }
        $this->_campaignFilter = $campaign;

        return $this;
    }

    /**
     * @param $variation
     *
     * @return $this
     */
    public function addVariationFilter($variation)
    {
        if ($variation instanceof Varien_Object) {
            $variation = $variation->getId();
        }
        $this->_variationFilter = $variation;

        return $this;
    }

    /**
     * A direct binding points to recipient directly,
     * means that we have a data field that points to the recipient
     * directly, not using any customer_id, order_id, email etc to get to it
     *
     * @param Zend_Db_Select $select
     *
     * @return Zend_Db_Select
     */
    public function addDirectBinding(Zend_Db_Select $select)
    {
        $this->_direct = clone $select;

        return $this->_direct;
    }

    /**
     * An indirect binding does not need to point to the recipient directly,
     * we can use the field like customer_id, email, or anything else to make the
     * link to the recipient.
     *
     * @param string $name
     * @param Zend_Db_Select $select
     * @param string $field
     *
     * @return $this
     */
    public function addIndirectBinding($name, Zend_Db_Select $select, $field)
    {
        $this->_bindings[$name] = array(
            'select' => $select,
            'field'  => $field
        );

        return $this;
    }

    /**
     * Check if we can bind
     *
     * @param string $name
     * @return string
     */
    public function getBinding($name)
    {
        if (isset($this->_bindings[$name])) {
            return $this->_bindings[$name]['field'];
        }

        return false;
    }

    /**
     * Bind using binging select
     *
     * @param string $name
     *
     * @return Zend_Db_Select
     */
    public function bind($name)
    {
        /* @var $select Zend_Db_Select */
        $select = clone $this->_bindings[$name]['select'];
        $this->_unionSelects[] = $select;

        return $select;
    }

    /**
     * Add where condition for recipient
     *
     * @param string $fieldName
     * @param mixed $condition
     *
     * @return $this
     */
    public function addFilter($fieldName, $condition)
    {
        $adapter = $this->getResourceHelper()->getReadAdapter();
        $fieldName = $adapter->quoteIdentifier("recipient.$fieldName");
        $this->_where[] = $adapter->prepareSqlCondition($fieldName, $condition);

        return $this;
    }

    /**
     * @param Zend_Db_Select $select
     *
     * @return bool
     */
    protected function _prepareSelect(Zend_Db_Select $select)
    {
        $fromPart = $select->getPart(Zend_Db_Select::FROM);

        // the recipient table is required!
        if (!isset($fromPart['recipient'])) {
            return false;
        }

        foreach ($this->_where as $where) {
            $select->where($where);
        }

        /*
        $select->where('`recipient`.`sent_at` IS NOT NULL');
        if ($this->_campaignFilter !== null) {
            $select->where('`recipient`.`campaign_id` = ?', $this->_campaignFilter);
        }
        if ($this->_variationFilter !== null) {
            $select->where('`recipient`.`variation_id` = ?', $this->_variationFilter);
        }
        */

        $select->columns($this->_columns, 'recipient');

        return true;
    }

    /**
     *
     * @return Mzax_Emarketing_Model_Resource_Helper
     */
    protected function getResourceHelper()
    {
        return Mage::getResourceSingleton('mzax_emarketing/helper');
    }
}
