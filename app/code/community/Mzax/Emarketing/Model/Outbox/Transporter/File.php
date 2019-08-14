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


/**
 * Simple transporter for testing
 * 
 * emails will be saved to ./var/mzax_emails/...
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.2.5
 */
class Mzax_Emarketing_Model_Outbox_Transporter_File
    extends Mzax_Mail_Transport_File
    implements Mzax_Emarketing_Model_Outbox_Transporter_Interface
{
    
    
    public function setup(Mzax_Emarketing_Model_Outbox_Email $email)
    {
        $path[] = Mage::getBaseDir('var');
        $path[] = 'mzax_emails' ;
        $path[] = 'campaign_' . $email->getCampaignId();
        $path[] = 'mail.txt';
        
        $this->setFile(implode(DS, $path));
        $this->setSaveHtml(true);
    }
    
    
}
