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
 * @version     0.4.3
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * 
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.4.3
 */
class Mzax_Emarketing_Block_Unsubscribe extends Mage_Core_Block_Template
{
    


    /**
     * Retrieve session model
     *
     * @return Mzax_Emarketing_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('mzax_emarketing/session');
    }
    
    
    
    /**
     * Retrieve address
     * 
     * @return string
     */
    public function getAddress()
    {
        return $this->getSession()->getLastAddress();
    }
    
    
    
    public function getFormKey()
    {
        return $this->getSession()->getFormKey();
    }
    
    
    
    public function getYesUrl()
    {
        return $this->getUrl('*/*/do');
    }
    
    public function getNoUrl()
    {
        return $this->getUrl('/');
    }
}
