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

/*
 * Usefull links
* @see https://www.jitbit.com/maxblog/18-detecting-outlook-autoreplyout-of-office-emails-and-x-auto-response-suppress-header/
* @see http://stackoverflow.com/questions/1027395/detecting-outlook-autoreply-out-of-office-emails/14320010#14320010
*
*
*
* Credits
* @see https://github.com/cfortune/PHP-Bounce-Handler/
* @see Andris [http://stackoverflow.com/a/14320010/413323]
*/





/**
 * 
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.2.5
 */
class Mzax_Bounce_Detector_Autoreply extends Mzax_Bounce_Detector_Abstract
{
    
    
    public static $headers = array(
        // !not X-Auto-Response-Suppress
        'auto-submitted',
        'x-autorespond',
        'auto-submitted',
        'precedence' => array('auto_reply'),
        'auto-submitted' => 'auto_reply'
    );
    
    
    public static $subjects = array(
        'Auto:',
        'Received:',
        'Automatic reply',
        'Autosvar',
        'Automatisk svar',
        'Automatisch antwoord',
        'Abwesenheitsnotiz',
        'Risposta Non al computer',
        'Automatisch antwoord',
        'Auto Response',
        'Respuesta automática',
        'Fuori sede',
        'Out of Office',
        'Frånvaro',
        'Réponse automatique',
    );
    
    
    public static $regex = array(
        '^\[?auto.{0,20}reply\]?',
        '^auto[ -]?response',
        '^Yahoo! auto response',
        '^Thank you for your email\.',
        '^Vacation.{0,20}(reply|respon)',
        '^out.?of (the )?office',
        '^(I am|I\'m).{0,20}\s(away|on vacation|on leave|out of office|out of the office)',
        "\350\207\252\345\212\250\345\233\236\345\244\215"   #sino.com,  163.com  UTF8 encoded
    );
    
    
    
    
    public function isAutoReply(Mzax_Bounce_Message $message)
    {
        if($header = $message->searchHeader(self::$headers)) {
            $message->info('autoreply_header', $header);
            return true;
        }
        
        $subject = trim($message->getSubject());
        if(!$subject) {
            return false;
        }
        
        foreach(self::$subjects as $needle) {
            if(stripos($subject, $needle) === 0) {
                $message->info('autoreply_subject', $needle);
                return true;
            }
        }
        
        foreach(self::$regex as $regex) {
            if(preg_match("/$regex/i", $subject, $matches)) {
                $message->info('autoreply_subject', $matches[0]);
                return true;
            }
        }
        return false;
    }
    
    
    
    
    
    public function inspect(Mzax_Bounce_Message $message)
    {
        if(!$this->isAutoReply($message)) {
            return false;
        }
        $message->info(Mzax_Bounce::TYPE_AUTOREPLY, true);
        $message->info('type', Mzax_Bounce::TYPE_AUTOREPLY);
        $message->info('recipient', $this->findEmail($message->getFrom()));
        
        return true;
        
    }
    
    
    
    
    
}