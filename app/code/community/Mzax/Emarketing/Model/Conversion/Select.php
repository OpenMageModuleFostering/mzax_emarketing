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
 * Class Mzax_Emarketing_Model_Conversion_Select
 */
class Mzax_Emarketing_Model_Conversion_Select
{
    /**
     * @var array[]
     */
    protected $_bindings = array();

    /**
     * @var Zend_Db_Select[]
     */
    protected $_unionSelects = array();

    /**
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
        $select = clone $this->_bindings[$name]['select'];
        $this->_unionSelects[] = $select;

        return $select;
    }
}
