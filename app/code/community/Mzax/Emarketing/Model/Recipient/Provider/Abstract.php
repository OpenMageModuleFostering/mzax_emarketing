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


abstract class Mzax_Emarketing_Model_Recipient_Provider_Abstract 
    extends Mzax_Emarketing_Model_Object_Filter_Main
{
    
    
    /**
     * 
     * @var Mzax_Emarketing_Model_Campaign
     */
    protected $_campaign;
    
    
    public function getTitle()
    {
        return 'Provider Title';
    }
    

    

    /**
     * Prepare recipient for sending
     * 
     * @param Mzax_Emarketing_Model_Recipient $recipient
     * @return void
     */
    public function prepareRecipient(Mzax_Emarketing_Model_Recipient $recipient)
    {
        $this->getObject()->prepareRecipient($recipient);
    }
    
    
    
    public function prepareSnippets(Mzax_Emarketing_Model_Medium_Email_Snippets $snippets)
    {
        $this->getObject()->prepareSnippets($snippets);
    }
    
    
    
    
    public function prepareParams()
    {
        if($this->_campaign) {
            $this->setParam('campaign', $this->_campaign);
            $this->setParam('store_id', $this->_campaign->getStoreId());
            
            $gmtOffset = Mage::app()->getLocale()->storeDate($this->_campaign->getStoreId())->getGmtOffset()/60;
            $this->setParam('gmt_offset', $gmtOffset);
        }
    }
    
    
    
    
    /**
     * 
     * @param Mzax_Emarketing_Model_Campaign $campaign
     * @return Mzax_Emarketing_Model_Recipient_Provider_Abstract
     */
    public function setCampaign(Mzax_Emarketing_Model_Campaign $campaign)
    {
        $this->_campaign = $campaign;
        $this->load($campaign->getFilterData());
        return $this;
    }

    
    
    

    /**
     * Retrieve campaign
     *
     * @return Mzax_Emarketing_Model_Campaign
     */
    public function getCampaign()
    {
        return $this->_campaign;
    }
    
        
    
    
    
    
    /**
     * Every recipient provider gets notified when a link is clicked
     * 
     * @param Mzax_Emarketing_Model_Link_Reference $linkReference
     */
    public function linkClicked(Mzax_Emarketing_Model_Link_Reference $linkReference)
    {}
    
    
    
    
    public function bindRecipients(Mzax_Emarketing_Model_Resource_Recipient_Goal_Binder $binder)
    {
    }
    
    
}
