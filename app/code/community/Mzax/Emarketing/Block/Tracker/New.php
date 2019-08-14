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
 * @version     0.4.3
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
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.4.3
 */
class Mzax_Emarketing_Block_Tracker_New extends Mzax_Emarketing_Block_Tracker_Edit
{


    public function getHeaderText()
    {
        return $this->__('New Conversion Goal Tracker');
    }

    
    public function getValidationUrl()
    {
        return null;
    }
    
    
    protected function _prepareLayout()
    {
    	parent::_prepareLayout();
    	
    	/* @var $tracker  Mzax_Emarketing_Model_Conversion_Tracker */
        $tracker = Mage::registry('current_tracker');
    	
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('save_and_continue');    	
    }
    
    
    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/new');
    }
    
    
}
