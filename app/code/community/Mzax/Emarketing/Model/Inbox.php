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
 * Class Mzax_Emarketing_Model_Inbox
 */
class Mzax_Emarketing_Model_Inbox
{
    /**
     * @var Mzax_Emarketing_Model_Config
     */
    protected $_config;

    /**
     * Mzax_Emarketing_Model_Outbox constructor.
     */
    public function __construct()
    {
        $this->_config = Mage::getSingleton('mzax_emarketing/config');
    }

    /**
     * Retrieve new email messages
     *
     * @return $this
     */
    public function downloadEmails()
    {
        if (!$this->_config->flag('mzax_emarketing/inbox/enable')) {
            return $this;
        }

        /** @var Mzax_Emarketing_Helper_Data $helper */
        $helper = Mage::helper('mzax_emarketing');

        $lock = $helper->lock('download_emails');
        if (!$lock) {
            return $this;
        }

        /* @var $collector Mzax_Emarketing_Model_Inbox_Email_Collector */
        $collector = Mage::getModel('mzax_emarketing/inbox_email_collector');
        $collector->collect();

        $lock->unlock();

        return $this;
    }

    /**
     * Parse email messages
     *
     * @return $this
     */
    public function parseEmails()
    {
        /** @var Mzax_Emarketing_Helper_Data $helper */
        $helper = Mage::helper('mzax_emarketing');

        $lock = $helper->lock('parse_emails');
        if (!$lock) {
            return $this;
        }

        /* @var $collection Mzax_Emarketing_Model_Resource_Inbox_Email_Collection */
        $collection = Mage::getResourceModel('mzax_emarketing/inbox_email_collection');
        $collection->addFieldToFilter('is_parsed', 0);

        /* @var $email Mzax_Emarketing_Model_Inbox_Email */
        foreach ($collection as $email) {
            $email->parse();
            $lock->touch();
        }

        $lock->unlock();

        return $this;
    }
}
