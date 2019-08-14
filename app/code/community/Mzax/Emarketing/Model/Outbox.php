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
 * Class Mzax_Emarketing_Model_Outbox
 */
class Mzax_Emarketing_Model_Outbox
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
     * Retrieve emails
     *
     * @param string[] $ids
     *
     * @return Mzax_Emarketing_Model_Resource_Outbox_Email_Collection
     */
    public function getEmails($ids = null)
    {
        /* @var $collection Mzax_Emarketing_Model_Resource_Outbox_Email_Collection */
        $collection = Mage::getResourceModel('mzax_emarketing/outbox_email_collection');
        $collection->assignRecipients();
        $collection->assignCampaigns();
        if ($ids) {
            $collection->addIdFilter($ids);
        }

        return $collection;
    }

    /**
     * Retrieve email by recipient
     *
     * @param Mzax_Emarketing_Model_Recipient|string $recipient
     *
     * @return Mzax_Emarketing_Model_Outbox_Email
     */
    public function getEmailByRecipient($recipient)
    {
        if ($recipient instanceof Mzax_Emarketing_Model_Recipient) {
            $recipient = $recipient->getId();
        }

        /* @var $email Mzax_Emarketing_Model_Outbox_Email */
        $email = Mage::getModel('mzax_emarketing/outbox_email');
        $email->load($recipient, 'recipient_id');

        return $email;
    }

    /**
     * Send out emails
     *
     * @param array $options
     *
     * @return int
     */
    public function sendEmails(array $options = array())
    {
        $options = new Varien_Object($options);

        $verbose = (bool)$options->getData('verbose');

        /** @var Mzax_Emarketing_Helper_Data $helper */
        $helper = Mage::helper('mzax_emarketing');

        $lock = $helper->lock('send_emails');
        if (!$lock) {
            if ($verbose) {
                echo "\nACITVE LOCK- STOP\n\n\n";
            }
            return 0;
        }

        $timeout  = $options->getDataSetDefault('timeout', 60*5);
        $maximum  = $options->getDataSetDefault('maximum', 200);
        $now      = $options->getDataSetDefault('now', time());
        $force    = $options->getDataSetDefault('force', false);
        $emailIds = $options->getDataSetDefault('ids', false);

        /* @var $emails Mzax_Emarketing_Model_Resource_Outbox_Email_Collection */
        $emails = Mage::getResourceModel('mzax_emarketing/outbox_email_collection');
        $emails->assignCampaigns();
        $emails->assignRecipients();
        $emails->addFieldToFilter('sent_at', array('null' => true));
        $emails->addFieldToFilter('status', Mzax_Emarketing_Model_Outbox_Email::STATUS_NOT_SEND);
        $emails->setOrder('expire_at', 'ASC');
        $emails->setPageSize($maximum);

        if (!$force) {
            $emails->addTimeFilter($now);
        }

        if (!empty($emailIds)) {
            $emails->addFieldToFilter('email_id', array('in' => $emailIds));
        }

        if ($verbose) {
            echo "\n\n{$emails->getSelect()}\n\n";
        }

        $domainThrottle = $this->enableDomainThrottling();
        if ($domainThrottle) {
            /* @var $domainThrottle Mzax_Emarketing_Model_DomainThrottle */
            $domainThrottle = Mage::getModel('mzax_emarketing/domainThrottle');
            $domainThrottle->setTimeThreshold($this->getConfig('time_threshold'));
            $domainThrottle->setSendThreshold($this->getConfig('send_threshold'));
            $domainThrottle->setRestTime($this->getConfig('rest_time'));

            $domainSpecific = $this->getConfig('domain_specific');
            if ($domainSpecific) {
                foreach (unserialize($domainSpecific) as $data) {
                    $domainThrottle->addDomainOption(
                        $data['domain'],
                        $data['time_threshold'],
                        $data['send_threshold'],
                        $data['rest_time']
                    );
                }
            }

            $domainThrottle->purge();
        }

        if ($verbose) {
            echo sprintf("found %s emails...\n", count($emails));
        }

        $start = time();
        $count = 0;

        /* @var $email Mzax_Emarketing_Model_Outbox_Email */
        foreach ($emails as $email) {
            // recipient may has been removed, if so - discard email
            if (!$email->getRecipient()->getId()) {
                $email->setStatus(Mzax_Emarketing_Model_Outbox_Email::STATUS_DISCARDED);
                $email->getLog()->warn("Recipient does not exist any more.");
                $email->save();
                continue;
            }

            if (time() - $start > $timeout) {
                $this->log("Mzax Emarketing: Reached timelimit of {$timeout}sec", $verbose);
                break;
            }

            // check if we can still send the message or if we missed the expire date
            if ($email->isExpired($now)) {
                $email->setStatus(Mzax_Emarketing_Model_Outbox_Email::STATUS_EXPIRED);
                $warn = "Message has expired, stop sending";
                $email->getLog()->warn($warn);
                $email->save();
                continue;
            }

            // check if we can send now if not ignore
            if (!$email->canSend($now)) {
                continue;
            }

            if ($domainThrottle && ($time = $domainThrottle->isResting($email->getDomain()))) {
                $notice = "DomainThrottle currently prevents this message from sending for at least $time more seconds";
                $this->log($notice, $verbose);
                $email->getLog()->notice($notice);
                $email->save();
                continue;
            }

            if ($verbose) {
                echo sprintf("try sending email %s to %s.\n", $email->getId(), $email->getTo());
            }

            $email->send($verbose);
            $lock->touch();
            $count++;
        }

        $lock->unlock();

        return $count;
    }

    /**
     * Log message
     *
     * @param string $message
     * @param boolean $verbose
     *
     * @return void
     */
    protected function log($message, $verbose = false)
    {
        /** @var Mzax_Emarketing_Helper_Data $helper */
        $helper = Mage::helper('mzax_emarketing');

        $helper->log($message);
        if ($verbose) {
            echo "$message\n";
        }
    }

    /**
     * Enable domain throttling
     * This will prevent to send to many emails in a to short
     * of time to the same domain
     *
     * @return boolean
     */
    public function enableDomainThrottling()
    {
        return $this->_config->flag('mzax_emarketing/domain_throttling/enable');
    }

    /**
     * Retrieve config option
     *
     * @param string $path
     * @return mixed
     */
    protected function getConfig($path)
    {
        return $this->_config->get('mzax_emarketing/domain_throttling/' . $path);
    }

    /**
     * Retrieve resource object
     *
     * @return Mzax_Emarketing_Model_Resource_Outbox_Email
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('mzax_emarketing/outbox_email');
    }
}
