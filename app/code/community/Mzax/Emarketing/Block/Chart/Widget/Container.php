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


use Mzax_Emarketing_Block_Chart_Abstract as Chart;

/**
 * Class Mzax_Emarketing_Block_Chart_Widget_Container
 */
class Mzax_Emarketing_Block_Chart_Widget_Container extends Mage_Adminhtml_Block_Abstract
{
    /**
     * @var Chart
     */
    protected $_chart;

    /**
     * Set chart
     *
     * @param Chart $chart
     *
     * @return $this
     */
    public function setChart(Chart $chart)
    {
        $this->_chart = $chart;

        return $this;
    }

    /**
     * Retrieve chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->_chart;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_chart) {
            return $this->_chart->toHtml();
        }

        return '';
    }
}
