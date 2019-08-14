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


class Mzax_Emarketing_Model_Resource_Conversion_Tracker_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    
    protected function _construct()
    {
        $this->_init('mzax_emarketing/conversion_tracker');
    }
    
    
    /**
     * Id filter
     *
     * @param mixed $trackerIds
     * @return Mzax_Emarketing_Model_Resource_Conversion_Tracker_Collection
     */
    public function addIdFilter($trackerIds)
    {
        $this->addFieldToFilter('tracker_id', array('in' => $trackerIds));
        return $this;
    }
    
    

    /**
     * Id filter
     *
     * @param mixed $trackerIds
     * @return Mzax_Emarketing_Model_Resource_Conversion_Tracker_Collection
     */
    public function addCampaignFilter($campaign)
    {
        if($campaign instanceof Mzax_Emarketing_Model_Campaign) {
            $campaign = $campaign->getId();
        }       
        
        $this->addFieldToFilter('campaign_ids', array(
            array('finset' => $campaign), 
            array('finset' => '*')
        ));
        
        
        return $this;
    }
    
    
    

    /**
     * Filter active trackers
     *
     * @return Mzax_Emarketing_Model_Resource_Campaign_Collection
     */
    public function addActiveFilter($flag = true)
    {
        $this->addFieldToFilter('is_active', $flag ? 1 : 0);
        return $this;
    }
    
    
    
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('tracker_id','title');
    }
    
    
    public function toOptionHash()
    {
        return $this->_toOptionHash('tracker_id','title');
    }
    
}