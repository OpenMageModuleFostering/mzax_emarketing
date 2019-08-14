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
 * Class Mzax_Emarketing_Block_Tracker_Edit_Tab_Conditions
 */
class Mzax_Emarketing_Block_Tracker_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return $this
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_mzax_emarketing');
        $form->setFieldNameSuffix('mzax_emarketing');

        $tracker = Mage::registry('current_tracker');

        $form->setHtmlIdPrefix('conditions_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('mzax/emarketing/fieldset.phtml')
            ->setTestUrl($this->getUrl('*/*/testConditions', array('_current' => true)))
            ->setNewFilterUrl($this->getUrl('*/*/newConditionHtml', array('tracker' => $tracker->getId())));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Only track conversion goals matching the conditions below')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
        ))->setTracker($tracker)->setRenderer(Mage::getBlockSingleton('mzax_emarketing/conditions'));

        $this->setForm($form);

        return $this;
    }
}
