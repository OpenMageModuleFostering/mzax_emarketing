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
 * Class Mzax_Emarketing_Block_System_Config_Form_Field_Mandrill
 */
class Mzax_Emarketing_Block_System_Config_Form_Field_Mandrill
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @return string
     */
    protected function _getHtml()
    {
        $username = Mage::getStoreConfig('mzax_emarketing/email/mandrill_username');
        $password = Mage::getStoreConfig('mzax_emarketing/email/mandrill_password');

        if (empty($username)) {
            $message = $this->__('Please provider a valid Mandrill username.');
            $class = 'inbox-failure';
        } elseif (empty($password)) {
            $message = $this->__('Please provider a valid Mandrill password.');
            $class = 'inbox-failure';
        } else {
            /* @var $transport Mzax_Emarketing_Model_Outbox_Transporter_Mandrill */
            $transport = Mage::getModel('mzax_emarketing/outbox_transporter_mandrill');

            $result = $transport->testAuth($username, $password);

            if ($result === true) {
                $message = $this->__('Successfully connected to Mandrill');
                $class = 'inbox-success';
            } else {
                $message = $this->__('Failed to connect to Mandrill: %s', $result);
                $class = 'inbox-failure';
            }
        }

        return '<div class="inbox-status '.$class.'">' . $message . '</div>';
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();

        if (Mage::getStoreConfig('mzax_emarketing/email/transporter') !== 'mandrill') {
            return '';
        }

        $html = '<tr id="row_' . $id . '">'
              .   '<td class="mzax-mail-storage-test" colspan="3">' . $this->_getHtml(). '</td>'
              . '</tr>';

        return $html;
    }
}
