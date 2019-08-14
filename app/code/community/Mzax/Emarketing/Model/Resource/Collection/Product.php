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
 * @version     0.4.5
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product Collection
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.4.5
 */
class Mzax_Emarketing_Model_Resource_Collection_Product extends Mage_Catalog_Model_Resource_Product_Collection
{
    
    /**
     * 
     * @var array
     */
    protected static $_eventTypes;
    
    
    
    
    
    /**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        return false;
    }
    
    
    
    
    /**
     * Add filter to only show products where the event
     * occured within the last X hours.
     * 
     * @param integer $hours
     * @return Mzax_Emarketing_Model_Resource_Collection_Product
     */
    public function addEventDateFilter($days)
    {
        $this->addAttributeToFilter('logged_at', 
            array('gt' => new Zend_Db_Expr($this->getConnection()
                    ->quoteInto('DATE_SUB(NOW(), INTERVAL ? DAY)', $days))));
        
        return $this;
    }
    
    
    
    
    /**
     * Add customer compare product event filter
     *
     * @param mixed $customer
     * @return Mzax_Emarketing_Model_Resource_Collection_Product
     */
    public function addCustomerWhishlistFilter($customer)
    {
        return $this->addCustomerEventFilter($customer, 'wishlist_add_product');
    }
    
    

    /**
     * Add customer compare product event filter
     *
     * @param mixed $customer
     * @return Mzax_Emarketing_Model_Resource_Collection_Product
     */
    public function addCustomerCompareFilter($customer)
    {
        return $this->addCustomerEventFilter($customer, 'catalog_product_compare_add_product');
    }
    
    
    
    /**
     * Add customer view product event filter
     * 
     * @param mixed $customer
     * @return Mzax_Emarketing_Model_Resource_Collection_Product
     */
    public function addCustomerViewFilter($customer)
    {
        return $this->addCustomerEventFilter($customer, 'catalog_product_view');
    }
    
    
    
    
    /**
     * Add customer report event filter
     * 
     * @param mixed $customer
     * @param string $event
     * @return Mzax_Emarketing_Model_Resource_Collection_Product
     */
    public function addCustomerEventFilter($customer, $event = false)
    {
        if($customer instanceof Varien_Object) {
            $customer = $customer->getId();
        }
        
        $cond = array(
            'subtype'    => 0,
            'subject_id' => $customer
        );
        
        if($event = $this->getEventTypeId($event)) {
            $cond['event_type_id'] = $event;
        }
        
        
        $this->joinTable(
               array('event' => 'reports/event'),
               'object_id=entity_id', 
               array('event_id'    => 'event_id', 
                     'logged_at'   => 'logged_at', 
                     'event_store' => 'store_id'), 
               $cond);
        
        $this->groupByAttribute('entity_id');
        
        return $this;
    }
    
    
    
    
    
    /**
     * Retrieve event type id from event name
     * If id is provided, check if exist
     * 
     * @param mixed $event
     * @return integer|false
     */
    public function getEventTypeId($event)
    {
        if(!self::$_eventTypes) {
            self::$_eventTypes = Mage::getResourceModel('reports/event_type_collection')->toOptionArray();
        }
        
        // assume event type id
        if(is_numeric($event)) {
            if(array_key_exists($event, self::$_eventTypes)) {
                return (int) $event;
            }
            return false;
        }
        return array_search($event, self::$_eventTypes);
        
    }
    
    
}