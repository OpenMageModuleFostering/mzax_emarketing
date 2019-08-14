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
 * Class Mzax_Emarketing_Block_Template_Grid
 */
class Mzax_Emarketing_Block_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mzax_Emarketing_Block_Template_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('template_grid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('template_id');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mzax_Emarketing_Model_Resource_Template_Collection */
        $collection = Mage::getResourceModel('mzax_emarketing/template_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => $this->__('Created At'),
            'index'     => 'created_at',
            'gmtoffset' => true,
            'type'      =>'datetime'
        ));

        $this->addColumn('updated_at', array(
            'header'    => $this->__('Updated At'),
            'index'     =>'created_at',
            'gmtoffset' => true,
            'type'      =>'datetime'
        ));

        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name',
        ));

        parent::_prepareColumns();

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
