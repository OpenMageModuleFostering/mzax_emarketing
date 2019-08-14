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


class Mzax_Emarketing_Block_Campaign_Edit_Tab_Tasks extends Mage_Adminhtml_Block_Template
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('mzax/emarketing/campaign/tasks.phtml');
    }
    
    
    /**
     * 
     * @return Mzax_Emarketing_Model_Campaign
     */
    public function getCampaign()
    {
        return Mage::registry('current_campaign');
    }
    
    
    
    /**
     * Retrieve action url
     * 
     * @return string
     */
    public function getActionUrl($action)
    {
        return $this->getUrl('*/*/' . $action, array(
            'id' => $this->getCampaign()->getId()
        ));
    }
    
    
}
