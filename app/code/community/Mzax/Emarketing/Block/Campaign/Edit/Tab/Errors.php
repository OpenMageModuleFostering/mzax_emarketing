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
 * Class Mzax_Emarketing_Block_Campaign_Edit_Tab_Errors
 */
class Mzax_Emarketing_Block_Campaign_Edit_Tab_Errors extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mzax_Emarketing_Block_Campaign_Edit_Tab_Errors constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('recipient_error_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mzax_Emarketing_Model_Resource_Recipient_Error_Collection */
        $collection = Mage::getResourceModel('mzax_emarketing/recipient_error_collection');

        if ($campaign = Mage::registry('current_campaign')) {
            $collection->addFieldToFilter('campaign_id', $campaign->getId());
        }

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare columns
     *
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

        $this->addColumn('recipient', array(
            'header'         => $this->__('Recipient'),
            'index'          => 'recipient_id'
        ));

        $this->addColumn('message', array(
            'header'    => $this->__('Message'),
            'index'     => 'message',
            'type'      => 'text',
            'getter'    => function ($row) {
                return nl2br($row->getMessage());
            },
            'truncate'  => 500
        ));

        parent::_prepareColumns();

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/errorGrid', array('grid_ajax' => 1, '_current'=> true));
    }

    /**
     * @param $row
     *
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    /**
     * @return bool
     */
    public function canDisplayContainer()
    {
        if ($this->getRequest()->getParam('grid_ajax')) {
            return false;
        }
        return true;
    }
}
