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
 * Class Mzax_Emarketing_Block_Tracker_Edit_Tab_Tasks
 */
class Mzax_Emarketing_Block_Tracker_Edit_Tab_Tasks extends Mage_Adminhtml_Block_Template
{
    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setTemplate('mzax/emarketing/tracker/tasks.phtml');
    }

    /**
     * Retrieve Current  Tracker
     *
     * @return Mzax_Emarketing_Model_Conversion_Tracker
     */
    public function getTracker()
    {
        return Mage::registry('current_tracker');
    }

    /**
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = array())
    {
        if (!isset($params['_tracker'])) {
            $params['id'] = $this->getTracker()->getId();
        }
        return parent::getUrl($route, $params);
    }
}
