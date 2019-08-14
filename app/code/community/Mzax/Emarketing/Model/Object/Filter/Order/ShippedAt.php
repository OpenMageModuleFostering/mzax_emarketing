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
 * Class Mzax_Emarketing_Model_Object_Filter_Order_ShippedAt
 *
 * @method string getShippedAtFrom()
 * @method $this setShippedAtFrom(string $value)
 *
 * @method string getShippedAtTo()
 * @method $this setShippedAtTo(string $value)
 *
 * @method string getShippedAtUnit()
 * @method $this setShippedAtUnit(string $value)
 */
class Mzax_Emarketing_Model_Object_Filter_Order_ShippedAt
    extends Mzax_Emarketing_Model_Object_Filter_Order_Abstract
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return "Order | Shipped ... ago";
    }

    /**
     * @param Mzax_Emarketing_Db_Select $query
     *
     * @return void
     */
    protected function _prepareQuery(Mzax_Emarketing_Db_Select $query)
    {
        $query->joinTable('order_id', 'sales/shipment', 'shipment');
        $query->group();
        $query->having($this->getTimeRangeExpr('MIN(`shipment`.`created_at`)', 'shipped_at', false));
    }

    /**
     * @param Mzax_Emarketing_Model_Object_Collection $collection
     *
     * @return void
     */
    protected function _prepareCollection(Mzax_Emarketing_Model_Object_Collection $collection)
    {
        parent::_prepareCollection($collection);
        $collection->addField('shipped_at', new Zend_Db_Expr('MIN(`shipment`.`created_at`)'));
    }

    /**
     * @param Mzax_Emarketing_Block_Filter_Object_Grid $grid
     *
     * @return void
     */
    public function prepareGridColumns(Mzax_Emarketing_Block_Filter_Object_Grid $grid)
    {
        parent::prepareGridColumns($grid);

        $grid->addColumn('shipped_at', array(
            'header'    => $this->__('Shipped At'),
            'width'     => '180px',
            'index'     => 'shipped_at',
            'gmtoffset' => true,
            'type'      =>'datetime'
        ));
    }

    /**
     * html for settings in option form
     *
     * @return string
     */
    protected function prepareForm()
    {
        return $this->__(
            'Order was shipped %s ago.',
            $this->getTimeRangeHtml('shipped_at')
        );
    }
}
