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
 * @version     0.3.0
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 
 * @author Jacob Siefer
 *
 */
abstract class Mzax_Emarketing_Model_Object_Filter_Product_Abstract
    extends Mzax_Emarketing_Model_Object_Filter_Abstract
{
    
    
    public function acceptParent(Mzax_Emarketing_Model_Object_Filter_Component $parent)
    {
        return $parent->hasBinding('product_id');
    }
    
    
    
    

}
