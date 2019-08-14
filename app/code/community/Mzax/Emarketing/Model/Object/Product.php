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
class Mzax_Emarketing_Model_Object_Product extends Mzax_Emarketing_Model_Object_Abstract
{
    
    public function _construct()
    {
        $this->_init('catalog/product');
    }
    
    
    
    public function getName()
    {
        return $this->__('Product');
    }
    

    
    
    public function getQuery()
    {
        $query = parent::getQuery();
        $query->addBinding('product_id', 'product_id');
    
        return $query;
    }
    
    
    
    public function prepareCollection(Mzax_Emarketing_Model_Object_Collection $collection)
    {
        parent::prepareCollection($collection);
        $collection->addField('sku');
    }
    
    
    
    
    
    public function getAdminUrl($id)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $id));
    }
    
    
    
    
}
