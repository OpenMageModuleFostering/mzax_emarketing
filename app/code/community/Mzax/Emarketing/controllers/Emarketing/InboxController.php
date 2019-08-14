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
 * Class Mzax_Emarketing_Emarketing_InboxController
 */
class Mzax_Emarketing_Emarketing_InboxController extends Mzax_Emarketing_Controller_Admin_Action
{
    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('eMarketing'))
             ->_title($this->__('Inbox'));

        $this->loadLayout();
        $this->_setActiveMenu('promo/emarketing');

        $this->_addContent(
            $this->getLayout()->createBlock('mzax_emarketing/inbox_view', 'mzax_emarketing')
        );

        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function emailAction()
    {
        $this->_initEmail();

        $this->_title($this->__('eMarketing'))
             ->_title($this->__('Inbox Email'));

        $this->loadLayout();
        $this->_setActiveMenu('promo/emarketing');
        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function parseAction()
    {
        $email = $this->_initEmail();
        if ($email) {
            $email->setNoForward(true);
            $email->parse();

            $this->_redirect('*/*/email', array('_current' => true));
            return;
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Init email
     *
     * @param string $idFieldName
     *
     * @return Mzax_Emarketing_Model_Inbox_Email
     */
    protected function _initEmail($idFieldName = 'id')
    {
        $id = (int) $this->getRequest()->getParam($idFieldName);

        /** @var Mzax_Emarketing_Model_Inbox_Email $email */
        $email = Mage::getModel('mzax_emarketing/inbox_email');
        if ($id) {
            $email->load($id);
        }

        Mage::register('current_email', $email);

        return $email;
    }

    /**
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('mzax_emarketing/inbox_grid')->toHtml());
    }

    /**
     * @return void
     */
    public function campaignGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('mzax_emarketing/campaign_edit_medium_email_tab_inbox')->toHtml());
    }

    /**
     * @return void
     */
    public function massDeleteAction()
    {
        $messages = $this->getRequest()->getPost('messages');
        if (!empty($messages)) {
            $rows = Mage::getResourceSingleton('mzax_emarketing/inbox_email')->massDelete($messages);
            if ($rows) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d email(s) have been deleted.', $rows)
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @return void
     */
    public function massParseAction()
    {
        $messages = $this->getRequest()->getPost('messages');
        if (!empty($messages)) {
            /* @var $collection Mzax_Emarketing_Model_Resource_Inbox_Email_Collection */
            $collection = Mage::getResourceModel('mzax_emarketing/inbox_email_collection');
            $collection->addIdFilter($messages);

            /* @var $email Mzax_Emarketing_Model_Inbox_Email */
            foreach ($collection as $email) {
                $email->setNoForward(true);
                $email->parse();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d email(s) have been parsed.', count($collection))
            );
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @return void
     */
    public function massForwardAction()
    {
        $messages = $this->getRequest()->getPost('messages');
        if (!empty($messages)) {
            /* @var $collection Mzax_Emarketing_Model_Resource_Inbox_Email_Collection */
            $collection = Mage::getResourceModel('mzax_emarketing/inbox_email_collection');
            $collection->addIdFilter($messages);
            $collection->assignCampaigns();
            $collection->assignRecipients();

            $count = 0;

            /* @var $email Mzax_Emarketing_Model_Inbox_Email */
            foreach ($collection as $email) {
                if ($email->forward()) {
                    $count++;
                } else {
                    $this->_getSession()->addWarning(
                        $this->__('Failed to forward email from "%s".', $email->getEmail())
                    );
                }
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d email(s) have been forwarded.', $count)
            );
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @return void
     */
    public function massReportAction()
    {
        $messages = $this->getRequest()->getPost('messages');
        if (!empty($messages)) {
            /* @var $collection Mzax_Emarketing_Model_Resource_Inbox_Email_Collection */
            $collection = Mage::getResourceModel('mzax_emarketing/inbox_email_collection');
            $collection->addIdFilter($messages);
            $collection->assignCampaigns();

            /* @var $email Mzax_Emarketing_Model_Inbox_Email */
            foreach ($collection as $email) {
                $email->report();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d email(s) have been reported. Thank You!', count($collection))
            );
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass email unsubscribe
     *
     * @return void
     */
    public function massUnsubscribeAction()
    {
        $messages = $this->getRequest()->getPost('messages');
        if (!empty($messages)) {
            /* @var $collection Mzax_Emarketing_Model_Resource_Inbox_Email_Collection */
            $collection = Mage::getResourceModel('mzax_emarketing/inbox_email_collection');
            $collection->addIdFilter($messages);
            $collection->assignRecipients();

            /* @var $email Mzax_Emarketing_Model_Inbox_Email */
            foreach ($collection as $email) {
                $address = $email->getEmail();
                Mage::getSingleton('mzax_emarketing/medium_email')
                    ->unsubscribe($address, sprintf('Admin, manual unsubscribe (%s)', $email->getId()));

                $this->_getSession()->addSuccess(
                    $this->__('Email `%s` has been unsubscribed.', $address)
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @return void
     */
    public function massFlagAction()
    {
        $messages = $this->getRequest()->getPost('messages');
        $type = $this->getRequest()->getPost('type');
        if (!empty($messages) && $type) {
            $rows = Mage::getResourceSingleton('mzax_emarketing/inbox_email')->massTypeChange($messages, $type);
            if ($rows) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d email(s) have been updated.', $rows)
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     * @return void
     * @throws Exception
     */
    public function fetchAction()
    {
        /* @var $inbox Mzax_Emarketing_Model_Inbox */
        $inbox = Mage::getSingleton('mzax_emarketing/inbox');
        try {
            $inbox->downloadEmails();
        } catch (Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Failed to fetch emails, check your inbox configuration'));
        }

        try {
            $inbox->parseEmails();
        } catch (Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Failed to parse emails'));
        }

        $this->_redirect('*/*/index');
    }

    /**
     * ACL check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $session = $this->_sessionManager->getAdminSession();

        return $session->isAllowed('promo/emarketing/email');
    }
}
