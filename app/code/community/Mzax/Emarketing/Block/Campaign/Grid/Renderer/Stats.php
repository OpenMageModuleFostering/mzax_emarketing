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
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Class Mzax_Emarketing_Block_Campaign_Grid_Renderer_Stats
 */
class Mzax_Emarketing_Block_Campaign_Grid_Renderer_Stats
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($row instanceof Mzax_Emarketing_Model_Campaign && $row->getSendingStats() > 0) {
            return $this->renderStats($row);
        }
        return '';

    }

    /**
     * @param Mzax_Emarketing_Model_Campaign $campaign
     *
     * @return string
     */
    public function renderStats(Mzax_Emarketing_Model_Campaign $campaign)
    {
        $html = array();

        $sendings     = $campaign->getSendingStats();
        $views        = $campaign->getViewStats();
        $interactions = $campaign->getInteractionStats();
        $conversions  = $campaign->getConversionStats();
        $fails        = $campaign->getFailStats();

        $html[] = sprintf('<div class="mzax-grid-stats" title="%s">', $this->__('%s Sendings', $sendings));
        $html[] = sprintf('<div class="mzax-grid-stat views" style="width:%01.3f%%" title="%s"></div>', (($views)/$sendings)*100, $this->__('%s Views', $views));
        $html[] = sprintf('<div class="mzax-grid-stat interactions" style="width:%01.3f%%" title="%s"></div>', (($interactions)/$sendings)*100, $this->__('%s Interactions', $interactions));
        $html[] = sprintf('<div class="mzax-grid-stat conversions" style="width:%01.3f%%" title="%s"></div>', (($conversions)/$sendings)*100, $this->__('%s Conversions', $conversions));
        $html[] = sprintf('<div class="mzax-grid-stat fails" style="width:%01.3f%%" title="%s"></div>', (($fails)/$sendings)*100, $this->__('%s Bounces and Optouts', $fails));
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
