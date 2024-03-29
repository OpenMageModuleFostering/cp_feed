<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Commerce Pundit Technologies
 * @package     CP_Feed
 * @copyright   Copyright (c) 2016 Commerce Pundit Technologies. (http://www.commercepundit.com)    
 * @author      <<Niranjan Gondaliya>>    
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Cp_Feed_Model_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    protected $_feed_categories = array();
    protected $_prepared_feed_categories = array(
        'inclusion' => array(),
        'exclusion' => array());

    public function isEnabledFlat()
    {
        return false;
    }

    protected function _productLimitationJoinPrice()
    {
        $filters = $this->_productLimitationFilters;
        if (empty($filters['use_price_index'])) {
            return $this;
        }

        $connection = $this->getConnection();

        $joinCond = $joinCond = join(' AND ', array(
            'price_index.entity_id = e.entity_id',
            $connection->quoteInto('price_index.website_id = ?', $filters['website_id']),
            $connection->quoteInto('price_index.customer_group_id = ?', $filters['customer_group_id'])
        ));

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart['price_index'])) {
            $minimalExpr = new Zend_Db_Expr(
                'IF(`price_index`.`tier_price`, LEAST(`price_index`.`min_price`, `price_index`.`tier_price`), `price_index`.`min_price`)'
            );
            $this->getSelect()->joinLeft(
                array('price_index' => $this->getTable('catalog/product_index_price')),
                $joinCond,
                array('price', 'tax_class_id', 'final_price', 'minimal_price' => $minimalExpr, 'min_price', 'max_price', 'tier_price')
            );

            // Set additional field filters
            if (isset($this->_priceDataFieldFilters) && is_array($this->_priceDataFieldFilters)) {
                foreach ($this->_priceDataFieldFilters as $filterData) {
                    $this->getSelect()->where(call_user_func_array('sprintf', $filterData));
                }
            }
        } else {
            $fromPart['price_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }

        return $this;
    }

    protected function _applyProductLimitations()
    {
        $this->_prepareProductLimitationFilters();
        $this->_productLimitationJoinWebsite();
        $this->_productLimitationJoinPrice();
        $filters = $this->_productLimitationFilters;

        if (!(isset($filters['category_id']) || count($this->_feed_categories)) && !isset($filters['visibility'])) {
            return $this;
        }

        $conditions = array(
            'cat_index.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id'])
        );
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }

        if (count($this->_feed_categories)) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.category_id IN(?)', $this->_feed_categories);
            $this->getSelect()->distinct();
        } else
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.category_id=?', $filters['category_id']);


        if (isset($filters['category_is_anchor'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }

        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index'])) {
            $fromPart['cat_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        } else {
            $this->getSelect()->join(
                array('cat_index' => $this->getTable('catalog/category_product_index')),
                $joinCond,
                array('cat_index_position' => 0)
            );
        }

        if (method_exists($this, '_productLimitationJoinStore'))
            $this->_productLimitationJoinStore();

        Mage::dispatchEvent('catalog_product_collection_apply_limitations_after', array(
            'collection' => $this
        ));

        return $this;
    }

    public function prepareFeedCategoryFilter($condition, $value)
    {

        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addIsActiveFilter();

        $exclusion = in_array($condition, array('neq', 'nlike', 'nin'));
        if ($exclusion) {
            switch ($condition) {
                case 'neq':
                    $condition = 'eq';
                    break;
                case 'nlike':
                    $condition = 'like';
                    break;
                case 'nin':
                    $condition = 'in';
                    break;
            }
        }
        if ($condition == 'like' || $condition == 'nlike') {
            $category = Mage::getModel('catalog/category')->load($value);
            $categories->addFieldToFilter('name', array($condition => '%' . $category->getName() . '%'));
        } else {
            $categories->addFieldToFilter('entity_id', array($condition => $value));
        }

        foreach ($categories as $_cat) {
            if ($exclusion) {
                $this->_prepared_feed_categories['exclusion'][] = intval($_cat->getId());
            } else {
                $this->_prepared_feed_categories['inclusion'][] = intval($_cat->getId());
            }
        }

        return $this;
    }

    public function addFeedCategoryFilter()
    {

        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addIsActiveFilter();

        $all_categories_ids = array();

        foreach ($categories as $_cat) {
            $all_categories_ids[] = intval($_cat->getId());
        }

        if (count($this->_prepared_feed_categories['exclusion'])) {
            $all_categories_ids = array_diff($all_categories_ids, $this->_prepared_feed_categories['exclusion']);
        }
        if (count($this->_prepared_feed_categories['inclusion'])) {
            $all_categories_ids = array_intersect($all_categories_ids, $this->_prepared_feed_categories['inclusion']);
        }

        $this->_feed_categories = $all_categories_ids;

        $this->_applyProductLimitations();

        return $this;
    }


}
