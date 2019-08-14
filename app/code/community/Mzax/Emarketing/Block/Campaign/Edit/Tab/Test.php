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


class Mzax_Emarketing_Block_Campaign_Edit_Tab_Test extends Mzax_Emarketing_Block_Filter_Test_Recursive
{

    
    /**
     * Retrieve filter
     * 
     * @return Mzax_Emarketing_Model_Object_Filter_Abstract
     */
    public function getFilter()
    {
        /* @var $campaign Mzax_Emarketing_Model_Campaign */
        $campaign = Mage::registry('current_campaign');
        return $campaign->getRecipientProvider()->getFilter();
    }
    
    
    
    
    
    
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = array())
    {
        $params['campaign'] = $this->getCampaign()->getId();
        return parent::getUrl($route, $params);
    }
    
    
    
    
    /**
     * Retrieve current campaign
     *
     * @return Mzax_Emarketing_Model_Campaign
     */
    public function getCampaign()
    {
        return Mage::registry('current_campaign');
    }
    
    
}
