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
 * @version     0.4.2
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mzax_Emarketing_Block_Campaign_SendTestMail extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        
        $this->_blockGroup = 'mzax_emarketing';
        $this->_controller = 'campaign';
        $this->_mode       = 'sendTestMail';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Send Email'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('back');
    }


    

    public function getHeaderText()
    {
        return $this->__('Send Test Mail');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validateTestMail', array('_current'=>true));
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
        return $this->getUrl('*/*/sendTestMailPost', array('_current' => true));
    }
    
    
}
