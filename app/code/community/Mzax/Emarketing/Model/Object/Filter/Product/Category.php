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
 * Class Mzax_Emarketing_Model_Object_Filter_Product_Category
 */
class Mzax_Emarketing_Model_Object_Filter_Product_Category
    extends Mzax_Emarketing_Model_Object_Filter_Product_Abstract
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return "Product | Category";
    }

    /**
     * @return string
     */
    public function getChooserUrl()
    {
        return 'adminhtml/promo_widget/chooser/attribute/category_ids/form/filter_conditions_fieldset';
    }

    /**
     * @param Mzax_Emarketing_Db_Select $query
     */
    protected function _prepareQuery(Mzax_Emarketing_Db_Select $query)
    {
        $categoryIds = $this->getData('value');
        if (empty($categoryIds)) {
            $query->where('FALSE = TRUE');
            return;
        }

        $categoryIds = $this->_explode($categoryIds);
        $operator = $this->getDataSetDefault('operator', '()');

        $query->joinTable('product_id', 'catalog/category_product', 'link')->group();
        $query->addBinding('category_id', 'category_id', 'link');

        if ($operator === '()') {
            $query->where('`link`.`category_id` IN(?)', $categoryIds);
        } else {
            $query->where('`link`.`category_id` NOT IN(?)', $categoryIds);
        }
    }

    /**
     * @param Mzax_Emarketing_Model_Object_Collection $collection
     *
     * @return void
     */
    protected function _prepareCollection(Mzax_Emarketing_Model_Object_Collection $collection)
    {
        parent::_prepareCollection($collection);

        $expr = $collection->joinAttribute('catname', 'category_id', 'catalog_category/name');

        $collection->addField('categories', new Zend_Db_Expr("GROUP_CONCAT($expr SEPARATOR ', ')"));

    }

    /**
     * @param Mzax_Emarketing_Block_Filter_Object_Grid $grid
     *
     * @return void
     */
    public function prepareGridColumns(Mzax_Emarketing_Block_Filter_Object_Grid $grid)
    {
        parent::prepareGridColumns($grid);

        $grid->addColumn('categories', array(
            'header'    => $this->__('Categories'),
            'index'     => 'categories',
        ));
    }

    /**
     * @return string
     */
    protected function prepareForm()
    {
        $operations = $this->helper()->getOperatorOptions(array('()', '!()'));

        $operatorElement = $this->getSelectElement('operator', '()', $operations);

        $valueElement = $this->getInputElement('value');
        $valueElement->setExplicitApply(true);
        $valueElement->setAfterElementHtml($this->getChooserTriggerHtml());

        return $this->__(
            'Product category %s %s',
            $operatorElement->toHtml(),
            $valueElement->toHtml()
        );
    }
}
