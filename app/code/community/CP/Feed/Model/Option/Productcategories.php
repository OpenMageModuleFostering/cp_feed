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
 
class Cp_Feed_Model_Option_Productcategories extends Varien_Object
{
    const REPEATER = '_';
    const PREFIX_END = '';
    protected $_options = array();
    /**
     * @param int $parentId
     * @param int $recursionLevel
     *
     * @return array
     */
    public function getOptionArray($parentId = 1, $recursionLevel = 3)
    {
        $recursionLevel = (int)$recursionLevel;
        $parentId       = (int)$parentId;
        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        $storeCategories = $category->getCategories($parentId, $recursionLevel, TRUE, FALSE, TRUE);
        foreach ($storeCategories as $node) {
            /* @var $node Varien_Data_Tree_Node */
            $this->_options[] = array(
                'label' => $node->getName(),
                'value' => $node->getEntityId()
            );
            if ($node->hasChildren()) {
                $this->_getChildOptions($node->getChildren());
            }
        }
        return $this->_options;
    }
    /**
     * @param Varien_Data_Tree_Node_Collection $nodeCollection
     */
    protected function _getChildOptions(Varien_Data_Tree_Node_Collection $nodeCollection)
    {
        foreach ($nodeCollection as $node) {
            /* @var $node Varien_Data_Tree_Node */
            $prefix = str_repeat(self::REPEATER, $node->getLevel() * 1) . self::PREFIX_END;
            $this->_options[] = array(
                'label' => $prefix . $node->getName(),
                'value' => $node->getEntityId()
            );
            if ($node->hasChildren()) {
                $this->_getChildOptions($node->getChildren());
            }
        }
    }
}