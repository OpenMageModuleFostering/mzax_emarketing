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
 * Class Mzax_Emarketing_Block_Conditions
 */
class Mzax_Emarketing_Block_Conditions implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Mzax_Emarketing_Model_Conversion_Goal_Abstract $goal */
        $goal = $element->getTracker()->getGoal();

        if ($goal) {
            $html = '<div class="filters">';
            $html .= $goal->getFilter()->asHtml();
            $html .= '</div>';

            return $html;
        }
        return '';
    }
}
