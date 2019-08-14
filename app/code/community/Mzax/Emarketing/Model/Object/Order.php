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
 * Class Mzax_Emarketing_Model_Object_Order
 */
class Mzax_Emarketing_Model_Object_Order extends Mzax_Emarketing_Model_Object_Abstract
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('sales/order');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->__('Order');
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getAdminUrl($id)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $id));
    }

    /**
     * @param Varien_Object $row
     *
     * @return mixed
     */
    public function getRowId(Varien_Object $row)
    {
        return $row->getIncrementId();
    }

    /**
     * @return Mzax_Emarketing_Db_Select
     */
    public function getQuery()
    {
        $query = parent::getQuery();
        $query->addBinding('store_id', 'store_id');
        $query->addBinding('order_id', 'entity_id');
        $query->addBinding('quote_id', 'quote_id');
        $query->addBinding('ordered_at', 'created_at');
        $query->addBinding('goal_time', 'created_at');
        $query->addBinding('customer_id', 'customer_id');
        $query->addBinding('email', 'customer_email');

        return $query;
    }

    /**
     * @param Mzax_Emarketing_Model_Object_Collection $collection
     *
     * @return void
     */
    public function prepareCollection(Mzax_Emarketing_Model_Object_Collection $collection)
    {
        parent::prepareCollection($collection);

        $collection->addField('store_id');
        $collection->addField('created_at');
        $collection->addField('status');
        $collection->addField('increment_id');
        $collection->addField('customer_id');
        $collection->addField('email');
    }

    /**
     * @param Mzax_Emarketing_Block_Filter_Object_Grid $grid
     *
     * @return void
     */
    public function prepareGridColumns(Mzax_Emarketing_Block_Filter_Object_Grid $grid)
    {
        /** @var Mage_Sales_Model_Order_Config $orderConfig */
        $orderConfig = Mage::getSingleton('sales/order_config');

        /** @var Mzax_Emarketing_Model_Object_Customer $customerObject */
        $customerObject = Mage::getSingleton('mzax_emarketing/object_customer');

        if (!Mage::app()->isSingleStoreMode()) {
            $grid->addColumn('store_id', array(
                'header'    => $this->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
                'width' => '20%',
            ));
        }

        $grid->addColumn('customer_id', array(
            'header'      => $this->__('Customer ID'),
            'index'       => 'customer_id',
            'id_field'    => 'customer_id',
            'label_field' => 'customer_id',
            'is_system'   => true,
            'width'       => '50px',
            'renderer'    => 'mzax_emarketing/recipients_column_renderer_object',
            'object'      => $customerObject
        ));

        $grid->addColumn('email', array(
            'header' => Mage::helper('sales')->__('Email'),
            'index' => 'email'
        ));

        $grid->addColumn('created_at', array(
            'header' => $this->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '120px',
        ));

        $grid->addColumn('status', array(
            'header' => $this->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => $orderConfig->getStatuses(),
        ));
    }
}
