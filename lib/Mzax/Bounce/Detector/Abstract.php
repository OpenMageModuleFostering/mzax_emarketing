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
 * 
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.2.5
 */
abstract class Mzax_Bounce_Detector_Abstract
{
    const REGEX_EMAIL = '/(?:[a-z0-9_\-]+(?:\.[_a-z0-9\-]+)*@(?:[_a-z0-9\-]+\.)+(?:[a-z]+))/i';
    
    
    
    
    abstract function inspect(Mzax_Bounce_Message $message);
    
    
    
    /**
     * Scan given text for an email address
     *
     * @param string $text
     * @return string|NULL
     */
    public function findEmail($text)
    {
        if(preg_match(self::REGEX_EMAIL, $text, $matches)) {
            // ignore e.g. redacted@aol.com or redacted@comcaset.com
            if(stripos($matches[0], 'redacted@') !== 0) {
                return $matches[0];
            }
        }
        return null;
    }
    
}