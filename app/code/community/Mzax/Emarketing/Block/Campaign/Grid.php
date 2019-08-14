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
 * @version     0.2.6
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * Campaign Grid
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.2.6
 */
class Mzax_Emarketing_Block_Campaign_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('campaign_grid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('campaign_id');
    }
    
    

    protected function _prepareCollection()
    {
        /* @var $collection Mzax_Emarketing_Model_Resource_Campaign_Collection */
        $collection = Mage::getResourceModel('mzax_emarketing/campaign_collection');
       
        if(!$this->getRequest()->getParam('archive')) {
            $collection->addArchiveFilter(false);
        }
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    
    
    protected function _prepareLayout()
    {
        if( $this->getRequest()->getParam('archive') ) {
            $this->setChild('archive_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label'   => $this->__('Hide Archived'),
                    'onclick' => "{$this->getJsObjectName()}.addVarToUrl('archive', 0); {$this->getJsObjectName()}.reload();",
                    'class'   => 'task'
                ))
            );
        }
        else {
            $this->setChild('archive_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label'   => $this->__('Show Archived'),
                    'onclick' => "{$this->getJsObjectName()}.addVarToUrl('archive', 1); {$this->getJsObjectName()}.reload();",
                    'class'   => 'task'
                ))
            );
        }
        
        return parent::_prepareLayout();
    }
    
    
    
    
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html = $this->getChildHtml('archive_button') . $html;
        
        return $html;
    }

    
    
    
    protected function _prepareColumns()
    {
        $this->addColumn('added_at', array(
            'header'    => Mage::helper('mzax_emarketing')->__('Date Added'),
            'index'     => 'created_at',
            'gmtoffset' => true,
            'type'      => 'datetime'
        ));

        $this->addColumn('modified_at', array(
            'header'    => $this->__('Last Change'),
            'index'     => 'updated_at',
            'gmtoffset' => true,
            'type'      => 'datetime'
        ));
        
        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name'
        ));
        
        
        $this->addColumn('recipients', array(
            'header'    => $this->__('Recipients'),
            'index'     => 'provider',
            'type'      => 'options',
            'width'     => 120,
            'options'   => Mage::getSingleton('mzax_emarketing/recipient_provider')->getOptionHash()
        ));
        
        $this->addColumn('medium', array(
            'header'    => $this->__('Medium'),
            'index'     => 'medium',
            'type'      => 'options',
            'width'     => 120,
            'options'   => Mage::getSingleton('mzax_emarketing/medium')->getMediums()
        ));
        
        $this->addColumn('running', array(
            'header'    => $this->__('Is running'),
            'index'     => 'running',
            'type'      => 'options',
            'width'     => 120,
            'options'   => array(
                0 => $this->__('No'),
                1 => $this->__('Yes')
            )
        ));
        
        $this->addColumn('stats', array(
            'header'   => Mage::helper('mzax_emarketing')->__('Stats'),
            'filter'   => false,
            'renderer' => 'mzax_emarketing/campaign_grid_renderer_stats'
        ));
        
        

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
