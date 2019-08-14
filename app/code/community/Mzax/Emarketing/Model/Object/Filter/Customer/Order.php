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
 * @version     0.2.6
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * 
 * @author Jacob Siefer
 *
 */
class Mzax_Emarketing_Model_Object_Filter_Customer_Order
    extends Mzax_Emarketing_Model_Object_Filter_Customer_Abstract
{
    const DEFAULT_AGGREGATOR = 'all';
    
    const DEFAULT_EXPECTATION = 'true';
    
    

    protected $_allowChildren = true;
    
    

    /**
     * (non-PHPdoc)
     * @see Mzax_Emarketing_Model_Object_Filter_Abstract::getTitle()
     */
    public function getTitle()
    {
        return "Customer | Has order(s)...";
    }
    
    
    
    public function getQuery()
    {
        $query = $this->getObject()->getQuery();
        $query->setColumn('customer_id');
        $query->setColumn('order_id');
    
        return $query;
    }
    
    
    /**
     *
     * @return Mzax_Emarketing_Model_Object_Order
     */
    public function getObject()
    {
        return Mage::getSingleton('mzax_emarketing/object_order');
    }
    
    
    
    /**
     * 
     * @return Zend_Db_Select
     */
    protected function _prepareQuery(Mzax_Emarketing_Db_Select $query)
    {
        $conditions  = $this->_getConditions();
        $aggregator  = $this->getDataSetDefault('aggregator',  self::DEFAULT_AGGREGATOR);
        $expectation = $this->getDataSetDefault('expectation', self::DEFAULT_EXPECTATION);
        
        $select = $this->_combineConditions($conditions, $aggregator, $expectation);
        
        /* Check if we are looking for customer with no orders,
         * Use current query object and alter it without breaking the query structure as
         * we need to union the result with the sub filters
         */
        if($this->checkIfMatchZero('orders')) {
            
            $customerId = new Zend_Db_Expr('`customer`.`entity_id`');
            $zeroOrderQuery = $this->getQuery();
            $zeroOrderQuery->joinRight(array('entity_id' => '{customer_id}'), 'customer/entity', 'customer');
            $zeroOrderQuery->where('{order_id} IS NULL');
            $zeroOrderQuery->addColumn('customer_id', $customerId);
            $zeroOrderQuery->group($customerId);
            $select = $this->_select()->union(array($zeroOrderQuery->getSelect(), $select));
        }        
        
        $select->useTemporaryTable($this->getTempTableName());
        
        $query->joinSelect('customer_id', $select, 'filter', 'customer_id');
        $query->having($this->getWhereSql('orders', 'COUNT(`order_id`)'));
        $query->group();
    }
    
    

    

    protected function _prepareCollection(Mzax_Emarketing_Model_Object_Collection $collection)
    {
        parent::_prepareCollection($collection);
        $collection->addField('customer_id');
        $collection->addField('orders', new Zend_Db_Expr('COUNT(`order_id`)'));
    }
    
    
    
    
    
    public function prepareGridColumns(Mzax_Emarketing_Block_Filter_Object_Grid $grid)
    {
        parent::prepareGridColumns($grid);
        
        $grid->addColumn('orders', array(
            'header' => $this->__('Matching Orders'),
            'index'  => 'orders',
        ));
    
        $grid->setDefaultSort('count_orders');
        $grid->setDefaultDir('DESC');
    
    }
    
    
    
    
    
    
    
    
    
    /**
     * (non-PHPdoc)
     * @see Mzax_Emarketing_Model_Object_Filter_Abstract::prepareForm()
     */
    protected function prepareForm()
    {
        $aggregatorElement  = $this->getSelectElement('aggregator',  self::DEFAULT_AGGREGATOR);
        $expectationElement = $this->getSelectElement('expectation', self::DEFAULT_EXPECTATION);
        
        return $this->__('If number of orders, with %s of these conditions %s, %s:',
            $aggregatorElement->toHtml(),
            $expectationElement->toHtml(),
            $this->getInputHtml('orders', 'numeric')
         );
    }
    
    

    

}
