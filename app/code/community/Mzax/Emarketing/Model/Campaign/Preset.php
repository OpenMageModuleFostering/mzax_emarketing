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
 * @version     0.2.7
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * 
 * @method string getName()
 * @method string getDescription()
 * @method string getFile()
 * @method string getFilename()
 * 
 * @author Jacob Siefer
 *
 */
class Mzax_Emarketing_Model_Campaign_Preset extends Varien_Object
{
    
    
    /**
     * Validate the version of the preset
     * against the installed extension version
     * 
     * @return boolean
     */
    public function validateVersion()
    {
        if($version = $this->getVersion()) {
            return (version_compare($version, Mage::helper('mzax_emarketing')->getVersion()) >= 0);
        }
        return true;
    }
    
    
    
    /**
     * Make new campaign from this preset
     * 
     * @return Mzax_Emarketing_Model_Campaign
     */
    public function makeCampaign()
    {
        /* @var $campaign Mzax_Emarketing_Model_Campaign */
        $campaign = Mage::getModel('mzax_emarketing/campaign');
        $campaign->addData($this->getData());
        $campaign->setPreset($this);
        $campaign->setName(null);
        
        if( $provider = $campaign->getRecipientProvider() ) {
            $provider->load($this->getFilterExport());
        }
        
        Mage::dispatchEvent('mzax_emarketing_preset_make_campaign', array(
            'preset'   => $this,
            'campaign' => $campaign
        ));
        
        return $campaign;
    }
    
    
    
    /**
     * Load by file
     * 
     * @param string $file
     * @return Mzax_Emarketing_Model_Campaign_Preset
     */
    public function loadByFile($file)
    {
        $this->_getResource()->loadByFile($this, $file);
        return $this;
        
    }
    
    
    /**
     * Load by filename
     * 
     * @param string $name
     * @return Mzax_Emarketing_Model_Campaign_Preset
     */
    public function load($name)
    {
        $this->_getResource()->load($this, $name);
        return $this;
    }
    
    
    

    /**
     * Retrieve resource model
     *
     * @return Mzax_Emarketing_Model_Resource_Campaign_Preset
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('mzax_emarketing/campaign_preset');
    }
    
}
