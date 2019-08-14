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
 * @version     0.4.0
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mzax_Emarketing_Block_Inbox_Email extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        
        $this->_blockGroup = 'mzax_emarketing';
        $this->_controller = 'inbox';
        $this->_mode       = 'email';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('delete', 'label', $this->__('Delete'));

    }
    
    
    public function getHeaderText()
    {
        $message = Mage::registry('current_email');
        if ($message->getId()) {
            return $this->htmlEscape($message->getSubject());
        }
        else {
            return $this->__('New Email');
        }
    }

    
    
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
    
    
    
    protected function _prepareLayout()
    {
     //   return false;
    	$this->_addButton('save_and_continue', array(
            'label'     => $this->__('Save And Continue Edit'),
            'onclick'   => 'editForm.submit(\''.$this->_getSaveAndContinueUrl().'\')',
            'class'     => 'save'
        ), 10);
    	
    	
    	$this->_addButton('parse', array(
	        'label'     => $this->__('Parse'),
	        'onclick'   => "setLocation('{$this->getUrl('*/*/parse', array('_current'  => true))}')"
    	), 10);
    	

    	return parent::_prepareLayout();
    }
    
    
    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/*/save');
    }
    
    
    protected function _getSaveAndContinueUrl()
    {
    	return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit'
        ));
    }
}
