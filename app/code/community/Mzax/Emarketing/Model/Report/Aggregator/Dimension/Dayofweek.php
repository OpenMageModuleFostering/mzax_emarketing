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



class Mzax_Emarketing_Model_Report_Aggregator_Dimension_Dayofweek
    extends Mzax_Emarketing_Model_Report_Aggregator_Dimension_Abstract
{
    
    
    
    public function getTitle()
    {
        return "dayofweek";
    }
    
    
    public function getValues()
    {
        return array('Monday', 'Thuesday', 'Wednesday', 
                     'Thursday', 'Friday', 'Saturday', 'Sunday');
    }
    
    
    
    public function getSqlValues()
    {
        return array(
            '2' => 'Monday', 
            '3' => 'Thuesday', 
            '4' => 'Wednesday', 
            '5' => 'Thursday',
            '6' => 'Friday', 
            '7' => 'Saturday', 
            '1' => 'Sunday'
        );
    }
    
    
    

    protected function prepareAggregationSelect(Mzax_Emarketing_Db_Select $select)
    {
        $select->addBinding('value', 'DAYOFWEEK({local_date})');
    }

    
    
    
    
    
}