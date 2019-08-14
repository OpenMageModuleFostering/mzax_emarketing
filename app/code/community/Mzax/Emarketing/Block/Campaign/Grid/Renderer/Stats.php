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

 
class Mzax_Emarketing_Block_Campaign_Grid_Renderer_Stats extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if($row instanceof Mzax_Emarketing_Model_Campaign && $row->getSendingStats() > 0) {
            return $this->renderStats($row);
        }
        return '';
        
    }
    
    
    
    public function renderStats(Mzax_Emarketing_Model_Campaign $campagin)
    {
        $html = array();
        
        $sendings     = $campagin->getSendingStats();
        $interactions = $campagin->getInteractionStats();
        $conversions  = $campagin->getConversionStats();
        $fails        = $campagin->getFailStats();
        
        $html[] = sprintf('<div class="mzax-grid-stats" title="%s">', $this->__('%s Sendings', $sendings));
        $html[] = sprintf('<div class="mzax-grid-stat interactions" style="width:%01.3f%%" title="%s"></div>', (($interactions)/$sendings)*100, $this->__('%s Interactions', $interactions));
        $html[] = sprintf('<div class="mzax-grid-stat conversions" style="width:%01.3f%%" title="%s"></div>', (($conversions)/$sendings)*100, $this->__('%s Conversions', $conversions));
        $html[] = sprintf('<div class="mzax-grid-stat fails" style="width:%01.3f%%" title="%s"></div>', (($fails)/$sendings)*100, $this->__('%s Bounces and Optouts', $fails));
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
    
    
    
}