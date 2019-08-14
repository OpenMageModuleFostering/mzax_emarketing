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
 * Class Mzax_Emarketing_Model_Object_Filter
 */
class Mzax_Emarketing_Model_Object_Filter implements Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    /**
     * @var Mage_Core_Model_Config_Element
     */
    protected $_config;

    /**
     * @var Mzax_Emarketing_Model_Object_Filter[]
     */
    protected $_filters;

    /**
     * Create filter by name or return null
     * if filter is not found
     *
     * @param string $name
     *
     * @return Mzax_Emarketing_Model_Object_Filter_Abstract|null
     */
    public function factory($name)
    {
        $config = $this->getConfig();
        if (!isset($config->$name)) {
            return null;
        }
        $config = $config->$name;
        $class  = $config->getClassName();

        $instance = new $class($config);

        return $instance;
    }

    /**
     * Retrieve All options
     *
     * @param bool $withEmpty
     *
     * @return string[][]
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        foreach ($this->getFilters() as $name => $filter) {
            $options[] = array(
                'value' => $name,
                'label' => $filter->getTitle()
            );
        }
        if ($withEmpty) {
            array_unshift($options, array('label'=>'', 'value'=>''));
        }

        return $options;
    }

    /**
     * Retrieve all available filters
     *
     * @return Mzax_Emarketing_Model_Object_Filter_Abstract[]
     */
    public function getFilters()
    {
        if (!$this->_filters) {
            $this->_filters = array();

            foreach ($this->getConfig()->children() as $name => $cfg) {
                $this->_filters[$name] = self::factory($name);
            }
        }
        return $this->_filters;
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     *
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getFilters();
        if (isset($options[$value])) {
            return $options[$value]->getTitle();
        }

        return false;
    }

    /**
     * Retrieve email marketing collection config
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = Mage::getConfig()->getNode('global/mzax_emarketing/filters');
        }

        return $this->_config;
    }
}
