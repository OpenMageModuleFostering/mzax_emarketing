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
 * @version     0.2.7
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



class Mzax_Emarketing_Model_Report_Aggregator_Dimension_Hour
    extends Mzax_Emarketing_Model_Report_Aggregator_Dimension_Abstract
{
    
    
    
    public function getTitle()
    {
        return "hour";
    }
    
    
    public function getValues()
    {
        for ($i = 0; $i < 24; $i++) {
            $values[] = sprintf('%02s', $i);
        }
        return $values;
    }
    
    
    
    public function getSqlValues()
    {
        return $this->getValues();
    }
    
    

    protected function prepareAggregationSelect(Mzax_Emarketing_Db_Select $select)
    {
        $select->addBinding('value', 'DATE_FORMAT({local_date}, "%H")');
    }
    
    
}