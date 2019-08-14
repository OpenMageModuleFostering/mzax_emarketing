<?php
/**
 * Mzax Emarketing (www.mzax.de)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with Magento in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @version     0.2.5
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */




/**
 * 
 * 
 * @author Jacob Siefer
 *
 */
abstract class Mzax_Emarketing_Model_Object_Abstract extends Varien_Object
{
    
    /**
     *
     * @var Mage_Core_Model_Resource_Abstract
     */
    protected $_resource;
    
    
    
    /**
     * 
     * @var string
     */
    protected $_entityId;
    
    
    
    
    /**
     * 
     * @return string
     */
    abstract function getName();
    
    
    
    
    
    protected function _init($entityId)
    {
        $this->_entityId = $entityId;
        $this->_resource = Mage::getResourceSingleton($entityId);
    }
    
    
    
    /**
     * Retreive resource model
     * 
     * Mage_Core_Model_Resource_Db_Abstract
     * Mage_Eav_Model_Entity_Abstract
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getResource()
    {
        return $this->_resource;
    }
    
    
    
    /**
     * Retrieve object table name
     * 
     * @return string
     */
    public function getObjectTable()
    {
        $resource = $this->getResource();
        if($resource instanceof Mage_Eav_Model_Entity_Abstract) {
            return $resource->getEntityTable();
        }
        if($resource instanceof Mage_Core_Model_Resource_Db_Abstract) {
            return $resource->getMainTable();
        }
        return null;
    }
    
    
    /** 
     * Retrieve object id field name
     * 
     * @return string
     */
    public function getIdFieldName()
    {
        $resource = $this->getResource();
        if($resource instanceof Mage_Eav_Model_Entity_Abstract) {
            return $resource->getEntityIdField();
        }
        if($resource instanceof Mage_Core_Model_Resource_Db_Abstract) {
            return $resource->getIdFieldName();
        }
        return null;
    }
    
    
    
    
    /**
     * Retrieve collection instance from object model
     * 
     * @return Mzax_Emarketing_Model_Object_Collection
     */
    public function getCollection()
    {
        /* @var $collection Mzax_Emarketing_Model_Object_Collection */
        $collection = Mage::getModel('mzax_emarketing/object_collection');
        $collection->setObject($this);
        
        return $collection;
    }
    
    
    
    
    /**
     * Retreive select object for this object
     * This method should be extended to add custom bindings
     * 
     * @return Mzax_Emarketing_Db_Select
     */
    public function getQuery()
    {
        $select = $this->getResourceHelper()->select();
        $select->from($this->getObjectTable());
        $select->addBinding('id', $this->getIdFieldName());
        $select->setColumn('id');
        
        return $select;
    }
    
    
    
    
    
    /**
     * Retrieve direct admin url for this model
     * use null of not available
     * 
     * Used by filter test grid columns to add a link
     * to the object when showing object ids (e.g. customer_id, order_id)
     * 
     * @param string $id
     * @return NULL|string
     */
    public function getAdminUrl($id)
    {
        return null;
    }
    
    
    
    /**
     * Retrieve row id for an object
     * 
     * @param Varien_Object $row
     * @return mixed
     */
    public function getRowId(Varien_Object $row)
    {
        return $row->getId();
    }
    

    
    /**
     * Retrieve form helper
     *
     * @return Mzax_Emarketing_Helper_Data
     */
    protected function helper()
    {
        return Mage::helper('mzax_emarketing');
    }
    
    
    
    /**
     * Translate
     *
     * @param string $message
     * @param string $args,...
     * @return string
     */
    protected function __()
    {
        return call_user_func_array(array($this->helper(), '__'), func_get_args());
    }
    
    


    /**
     * Get admin url
     *
     * @param string $routePath
     * @param array $routeParams
     * @return string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        return $this->_getUrlModel()->getUrl($routePath, $routeParams);
    }
    
    
    
    
    /**
     * Retrieve admin url model
     * 
     * @see Mzax_Emarketing_Model_Object_Abstract::getUrl()
     * @return Mage_Adminhtml_Model_Url
     */
    protected function _getUrlModel()
    {
        return Mage::getSingleton('adminhtml/url');
    }
    
    
    
    
    /**
     * Retrieve resource helper
     * 
     * @return Mzax_Emarketing_Model_Resource_Helper
     */
    protected function getResourceHelper()
    {
        return Mage::getResourceSingleton('mzax_emarketing/helper');
    }
    
    
    //--------------------------------------------------------------------------
    //
    //  Recipient Grid Methods
    //
    //--------------------------------------------------------------------------
    
    
    /**
     * Prepare collection for use in object grid
     * 
     * @param Mzax_Emarketing_Model_Object_Collection $collection
     * @see Mzax_Emarketing_Model_Object_Abstract::prepareGridColumns()
     * @return void
     */
    public function prepareCollection(Mzax_Emarketing_Model_Object_Collection $collection)
    {
        $collection->setObject($this);
    }
    
    
    
    /**
     * Prepare the magento grid for this object
     * Can be overwritten to add default columns to a filter grid
     * used for the filter testing.
     * 
     * Use 'prepareCollection()' method to add all necesery data for
     * the grid frist
     * 
     * @see Mzax_Emarketing_Model_Object_Abstract::prepareCollection()
     * @param Mzax_Emarketing_Block_Filter_Object_Grid $grid
     * @return void
     */
    public function prepareGridColumns(Mzax_Emarketing_Block_Filter_Object_Grid $grid)
    {}
    
    
    /**
     * 
     * @deprecated
     * @param Mzax_Emarketing_Block_Filter_Object_Grid $grid
     */
    public function afterGridLoadCollection(Mzax_Emarketing_Block_Filter_Object_Grid $grid)
    {}
    
    
    
    /**
     * 
     * @deprecated
     * @param unknown $collection
     */
    public function afterLoadCollection($collection)
    {}
    

    
    
    public function prepareSnippets(Mzax_Emarketing_Model_Medium_Email_Snippets $snippets)
    {}
    
    
    /**
     * Prepare recipient for sending
     * this should at least set email and name to the recipient
     * but can also set any other data that later can be used
     * inside the templates
     *
     * @param Mzax_Emarketing_Model_Recipient $recipient
     */
    public function prepareRecipient(Mzax_Emarketing_Model_Recipient $recipient)
    {
        $object = Mage::getModel($this->_entityId);
        $object->load($recipient->getObjectId());
        
        $recipient->setObject($object);
    }
    
    
}
