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
 * Class Mzax_Emarketing_Model_Object_OrderAddress
 */
class Mzax_Emarketing_Model_Object_OrderAddress extends Mzax_Emarketing_Model_Object_Address
{
    /**
     * Model Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('sales/order_address');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->__('Order Address');
    }

    /**
     * @return Mzax_Emarketing_Db_Select
     */
    public function getQuery()
    {
        $query = parent::getQuery();
        $query->addBinding('order_id', 'parent_id');

        return $query;
    }
}
