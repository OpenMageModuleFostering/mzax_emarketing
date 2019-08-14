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
 * @author Jacob Siefer
 *
 */
class Mzax_Emarketing_Block_System_Config_Form_Field_MailStorage
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    
    
    
    protected function _getHtml()
    {
        /* @var $inbox Mzax_Emarketing_Model_Inbox_Email_Collector */
        $collector = Mage::getSingleton('mzax_emarketing/inbox_email_collector');
        $result = $collector->test();
        
        if($result) {
            $message = Mage::helper('mzax_emarketing')->__('Successfully conntected to inbox');
            $class = 'inbox-success';
        }
        else {
            $message = Mage::helper('mzax_emarketing')->__('Failed to conntected to inbox');
            $class = 'inbox-failure';
        }
        
        return '<div class="inbox-status '.$class.'">' . $message . '</div>';
    }
    
    
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        
        if(!Mage::getStoreConfigFlag('mzax_emarketing/inbox/enable')) {
            return '';
        }
    
        $useContainerId = $element->getData('use_container_id');
        $html = '<tr id="row_' . $id . '">'
              .   '<td class="mzax-mail-storage-test" colspan="3">' . $this->_getHtml(). '</td>'
              . '</tr>';
        return $html;
    }
}
