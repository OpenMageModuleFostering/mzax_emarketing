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
 * Class Mzax_Emarketing_EmailController
 */
class Mzax_Emarketing_EmailController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mzax_Emarketing_Model_SessionManager
     */
    protected $_sessionManager;

    /**
     * @var Mzax_Emarketing_Model_Outbox
     */
    protected $_outbox;

    /**
     * Controller Constructor.
     * Load dependencies.
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->_sessionManager = Mage::getSingleton('mzax_emarketing/sessionManager');
        $this->_outbox = Mage::getSingleton('mzax_emarketing/outbox');
    }

    /**
     * View email in browser action
     *
     * @return void
     */
    public function indexAction()
    {
        $session = $this->_sessionManager->getSession();

        $recipientId = $session->getLastRecipientId();
        if (!$recipientId) {
            $this->_redirectUrl('/');
            return;
        }

        $email = $this->_outbox->getEmailByRecipient($recipientId);
        if (!$email->getId() || $email->isPurged()) {
            $this->_redirectUrl('/');
            return;
        }

        $this->getResponse()->setBody($email->getBodyHtml());
    }
}
