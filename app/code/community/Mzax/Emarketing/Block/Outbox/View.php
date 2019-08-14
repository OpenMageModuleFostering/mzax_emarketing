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


class Mzax_Emarketing_Block_Outbox_View extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
    	$this->_blockGroup = 'mzax_emarketing';
        $this->_controller = 'outbox';
        $this->_headerText = Mage::helper('mzax_emarketing')->__('Manage Outbox');
        
        parent::__construct();
        
        
        $this->_removeButton('add');
        
        $this->_addButton('send', array(
            'label'     => $this->__('Send Pending'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/send') .'\')',
            'class'     => '',
        ));
    }

    
    
}
