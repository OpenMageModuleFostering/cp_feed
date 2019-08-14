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

class CP_Feed_Adminhtml_ItemsController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('cp_feed')->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Manager'), Mage::helper('adminhtml')->__('Feed Manager'));
        
        return $this;
    }
    
    public function indexAction()
    {
        $this->_initAction();
        $prefix = Mage::getConfig()->getTablePrefix();
        $table  = $prefix . "cp_feed";
        
        $istableExist = Mage::getSingleton('core/resource')->getConnection('backup_write')->showTableStatus($table);
        $write        = Mage::getSingleton('core/resource')->getConnection('core_write');
        if (!is_array($istableExist)) {
            $write->query("CREATE TABLE " . $prefix . "cp_feed(
			  id smallint(6) NOT NULL auto_increment,
			  vartimestamp varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='CP Feed' AUTO_INCREMENT=1;");
            
            $write->query("INSERT INTO " . $prefix . "cp_feed(id,vartimestamp) values(1,'321')");
        }
        $this->renderLayout();
        
    }
    
    /*	public function writeTempData(){
    if($feed_id = $this->getRequest()->getParam('id')){
    
    $feed = Mage::getModel('cp_feed/item')->load($feed_id);
    $start = intval($this->getRequest()->getParam('start'));
    $length = intval($this->getRequest()->getParam('length'));
    $feed->writeTempFile($start, $length);
    }
    
    }*/
    
    public function saveAction()
    {
        
        if ($data = $this->getRequest()->getPost()) {
            
            try {
                $id = $this->getRequest()->getParam('id');
                
                $model = Mage::getModel('cp_feed/item');
                
                
                if (isset($data['field'])) {
                    $content_data        = array();
                    $content_data_sorted = array();
                    
                    foreach ($data['field'] as $field) {
                        if (intval($field['order']) && !isset($content_data_sorted[$field['order']])) {
                            
                            $content_data_sorted[intval($field['order'])] = $field;
                            
                        } else {
                            
                            $field['order'] = 0;
                            $content_data[] = $field;
                        }
                        
                    }
                    
                    ksort($content_data_sorted);
                    
                    $data['content'] = json_encode(array_merge($content_data, $content_data_sorted));
                    
                }
                
                /*if(isset($data['filter']) && is_array($data['filter'])){
                
                $data['filter'] = json_encode(array_merge($data['filter'], array()));
                
                }else{
                $data['filter'] = json_encode(array());
                }*/
                
                if (isset($data['upload_day']) && is_array($data['upload_day'])) {
                    
                    $data['upload_day'] = implode(',', $data['upload_day']);
                    
                }
                
                if (isset($data['product_categories']) && is_array($data['product_categories'])) {
                    
                    $data['product_categories'] = implode(',', $data['product_categories']);
                    
                }
                
                /*	if (isset($data['upload_interval']) && in_array($data['upload_interval'], array(12,24))){
                $data['upload_hour_to'] = null;
                }
                */
                $model->setData($data)->setId($id)->save();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('core')->__('Data successfully saved'));
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                    return;
                }
                
            }
            catch (Mage_Core_Exception $e) {
                
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                
                Mage::getSingleton('core/session')->setFeedData($data);
                
                if ($model->getId() > 0) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                } else {
                    $this->_redirect('*/*/new', array(
                        'type' => $model->getType()
                    ));
                }
                return false;
                
            }
            catch (Exception $e) {
            
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Can’t save data'));
                
                Mage::getSingleton('core/session')->setFeedData($data);
                
                if ($model->getId() > 0) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                } else {
                    $this->_redirect('*/*/new', array(
                        'type' => $model->getType()
                    ));
                }
                return false;
            
            }
            $this->_redirect('*/*/');
        }
    }
    
    public function deleteAction()
    {
        
        if ($id = intval($this->getRequest()->getParam('id'))) {
            
            $this->_deleteItems(array(
                $id
            ));
            
        }
        $this->_redirect('*/*/');
    }
    
    public function massDeleteAction()
    {
        
        if ($ids = $this->getRequest()->getParam('id')) {
            if (is_array($ids) && !empty($ids)) {
                $this->_deleteItems($ids);
            }
            
        }
        
        $this->_redirect('*/*/');
        
    }
    
    
    protected function _deleteItems($ids)
    {
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                
                $item = Mage::getModel('cp_feed/item')->load($id);
                $item->delete();
                
            }
        }
    }
    
    public function newAction()
    {
        $this->_initAction();
        
        if ($data = Mage::getSingleton('core/session')->getFeedData()) {
            Mage::register('cp_feed', Mage::getModel('cp_feed/item')->addData($data));
            Mage::getSingleton('core/session')->setFeedData(null);
        }
        
        $this->_addContent($this->getLayout()->createBlock('cp_feed/adminhtml_items_edit'))->_addLeft($this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tabs'));
        
        $this->renderLayout();
        
    }
    
    public function editAction()
    {
        
        $this->_initAction();
        
        if ($id = $this->getRequest()->getParam('id')) {
            Mage::register('cp_feed', Mage::getModel('cp_feed/item')->load($id));
        }
        
        $this->_addContent($this->getLayout()->createBlock('cp_feed/adminhtml_items_edit'))->_addLeft($this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tabs'));
        
        $this->renderLayout();
        
    }
    
    public function uploadAction()
    {
        
        if ($id = $this->getRequest()->getParam('id')) {
            
            $item = Mage::getModel('cp_feed/item')->load($id);
            
            try {
                
                if ($item->ftpUpload()) {
                    
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('core')->__('File "%s" was uploaded!', $item->getFilename()));
                    
                }
                
            }
            catch (Mage_Core_Exception $e) {
                
                Mage::getSingleton('adminhtml/session')->addError($item->getFilename() . ' - ' . $e->getMessage());
                
            }
            catch (Exception $e) {
            
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('%s - Can\'t upload. Please, check your FTP Settings or Hosting Settings', $item->getFilename()));
            
            }
            
            return $this->_redirect('*/*/edit', array(
                'id' => $id
            ));
            
        }
        
        $this->_redirect('*/*/index');
        
    }
    public function addStockToCollection($collection)
    {
        $id = $this->getRequest()->getParam('id');
        
        $feed              = Mage::getModel('cp_feed/item')->load($id);
        $manageStockConfig = Mage::getStoreConfig('cataloginventory/item_options/manage_stock', $feed->getStoreId());
        $stkConditions     = 'e.entity_id=stk.product_id';
        
        if ($manageStockConfig) {
            // System Manage stock is On
            $ifCase = $feed->_getCheckSql('(stk.use_config_manage_stock = 1 OR ( stk.use_config_manage_stock = 0 AND stk.manage_stock = 1) )', 'stk.is_in_stock', '1');
        } else {
            // System Manage stock is On
            $ifCase = $feed->_getCheckSql('((stk.use_config_manage_stock = 0 AND stk.manage_stock = 0 ) OR (stk.use_config_manage_stock = 1))', '1', 'stk.is_in_stock');
        }
        
        $collection->getSelect()->join(array(
            'stk' => $collection->getTable('cataloginventory/stock_item')
        ), $stkConditions, array(
            'is_in_stock' => $ifCase,
            'manage_stock',
            'use_config_manage_stock'
        ));
        //        die((string)$collection->getSelect());
    }
    
    public function getProductsCollection($filterData = '', $current_page = 0, $length = 50)
    {
        
        $id = $this->getRequest()->getParam('id');
        
        $feed       = Mage::getModel('cp_feed/item')->load($id);
        //if (is_null($this->_productCollection) && $this->getId()){
        $collection = Mage::getModel('cp_feed/product_collection')->addAttributeToSelect('*');
        $collection->addStoreFilter(Mage::app()->getStore());
        /*if($length != 0){
        $collection->setPage($current_page+1, $length);
        }*/
        
        $fileDir = sprintf('%s\productsfeed', Mage::getBaseDir('media'));
        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())->addMinimalPrice()->addFinalPrice()->addTaxPercents()->addUrlRewrite(Mage::app()->getStore()->getRootCategoryId());
        //$this->addStockToCollection($collection);
        // Filter by Stock
        if ($feed->getUseLayer()) {
            //   filter only in stock product
            //   addSaleableFilterToCollection is required
            //   for Configurable products to properly manage the stock
            
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            
            Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        }
        // Export disable products
        if ($feed->getUseDisabled() == 1) {
            $collection->addAttributeToFilter('status', 1);
        }
        
        if ($feed->getProductCategories() != '') {
            $categoryids = $myArray = explode(',', $feed->getProductCategories());
            $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left');
            $collection->addAttributeToFilter('category_id', array(
                'in' => array(
                    $categoryids
                )
            ));
            $collection->getSelect()->group('e.entity_id');
        }
        
        if ($feed->getProductVisibility() != '' && $feed->getProductVisibility() != '0') {
            //echo $feed->getProductVisibility();
            $collection->addAttributeToFilter('visibility', $feed->getProductVisibility());
            //echo 'Cnt--'.count($collection);
            //exit;
        }
        //exit;
        // Filter Disabled
        if ($feed->getUseDisabled()) {
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            $collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        }
        $this->_productCollection = $collection;
        //}
        return $this->_productCollection;
    }
    
    public function getParentProduct(Varien_Object $product, $collection = null)
    {
        $childId = $product->getId();
        if (!isset($this->_parentProductsCache[$childId])) {
            $connection     = Mage::getSingleton('core/resource')->getConnection('read');
            $table          = Mage::getSingleton('core/resource')->getTableName('catalog_product_relation');
            $sql            = 'SELECT `parent_id` FROM ' . $table . ' WHERE `child_id` = ' . intval($childId);
            $parent_id      = $connection->fetchOne($sql);
            $parent_product = null;
            if ($parent_id) {
                if ($collection) {
                    //$parent_product = $collection->getItemById($parent_id);
					$parent_product = Mage::getModel('catalog/product')->load($parent_id);
                }
                if (!$parent_product->getId()) {
                    $parent_product = Mage::getModel('catalog/product')->load($parent_id);
                }
                $this->_parentProductsCache[$childId] = $parent_product;
            } else {
                $this->_parentProductsCache[$childId] = new Varien_Object();
            }
        }
        return $this->_parentProductsCache[$childId];
    }
    public function getCategoriesCollection()
    {
        if (is_null($this->_categoryCollection)) {
            $this->_categoryCollection = Mage::getResourceModel('catalog/category_collection')->addAttributeToSelect('name');
        }
        return $this->_categoryCollection;
    }
    
    public function generateAction()
    {
        
        if ($id = $this->getRequest()->getParam('id')) {
            
            try {
                
                $feed = Mage::getModel('cp_feed/item')->load($id);
                
                $fileDir     = sprintf('%s/productsfeed', Mage::getBaseDir('media'));
                $filePath    = sprintf('%s/productsfeed/%s', Mage::getBaseDir('media'), $feed->getFilename());
                $logFilepath = sprintf('%s/productsfeed/%s', Mage::getBaseDir('media'), 'log-' . $feed->getId() . '.txt');
                @unlink($filePath);
                @unlink($logFilepath);
                $filePath = sprintf('%s/productsfeed/%s', Mage::getBaseDir('media'), $feed->getFilename());
                
                if (!file_exists($fileDir)) {
                    mkdir($fileDir);
                    chmod($fileDir, 0777);
                }
                
                if (is_dir($fileDir)) {
                    
                    switch ($feed->getDelimiter()) {
                        case ('comma'):
                        default:
                            $delimiter = ",";
                            break;
                        case ('tab'):
                            $delimiter = "\t";
                            break;
                        case ('colon'):
                            $delimiter = ":";
                            break;
                        case ('space'):
                            $delimiter = " ";
                            break;
                        case ('vertical pipe'):
                            $delimiter = "|";
                            break;
                        case ('semi-colon'):
                            $delimiter = ";";
                            break;
                    }
                    //$delimiter = $this->getDelimiter();
                    switch ($feed->getEnclosure()) {
                        case (1):
                        default:
                            $enclosure = "'";
                            break;
                        case (2):
                            $enclosure = '"';
                            break;
                        case (3):
                            $enclosure = ' ';
                            break;
                    }
                    
                    $maping = json_decode($feed->getContent());
                    $fp     = fopen($filePath, 'w');
                    if ($feed->getData('use_addition_header') == 1) {
                        fwrite($fp, $feed->getData('addition_header'));
                    }
                    if ($feed->getShowHeaders()) {
                        $fields = array();
                        foreach ($maping as $col) {
                            $fields[] = $col->name;
                            $codes[]  = $col->attribute_value;
                        }
                        
                        fputcsv($fp, $fields, $delimiter, $enclosure);
                        
                    }
                    
                    
                    $attributes = Mage::getModel('eav/entity_attribute')->getCollection()->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getEntityType()->getData('entity_type_id'))->setCodeFilter($codes);
                    
                    $collection = $this->getProductsCollection();
                    
                    foreach ($collection as $product) {
                        $fields   = array();
                        $category = null;
                        foreach ($product->getCategoryIds() as $id) {
                            $_category = $this->getCategoriesCollection()->getItemById($id);
                            if (is_null($category) || ($category && $_category && $category->getLevel() < $_category->getLevel())) {
                                $category = $_category;
                            }
                        }
                        if ($category) {
                            
                            $store         = Mage::getModel('core/store')->load($feed->getStoreId());
                            $root_category = Mage::getModel('catalog/category')->load($store->getRootCategoryId());
                            $category_path = array(
                                $category->getName()
                            );
                            $parent_id     = $category->getParentId();
                            if ($category->getLevel() > $root_category->getLevel()) {
                                while ($_category = $this->getCategoriesCollection()->getItemById($parent_id)) {
                                    if ($_category->getLevel() <= $root_category->getLevel()) {
                                        break;
                                    }
                                    $category_path[] = $_category->getName();
                                    $parent_id       = $_category->getParentId();
                                }
                            }
                            $product->setCategory($category->getName());
                            $product->setCategoryId($category->getEntityId());
                            $product->setCategorySubcategory(implode(' > ', array_reverse($category_path)));
                        } else {
                            $product->setCategory('');
                            $product->setCategorySubcategory('');
                        }
                        $parent_product = $this->getParentProduct($product, $collection);
                        $_prod          = Mage::getModel('catalog/product')->load($product->getId());
                        foreach ($maping as $col) {
                            $value = null;
                            if ($col->attribute_value) {
                                switch ($col->attribute_value) {
                                    case ('parent_sku'):
                                        if ($parent_product && $parent_product->getEntityId()) {
                                            $value = $parent_product->getSku();
                                        } else {
                                            $value = '';
                                        }
                                        break;
                                    case ('price'):
                                        if (in_array($product->getTypeId(), array(
                                            Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                                            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                                        )))
                                            $value = $store->convertPrice($product->getMinimalPrice(), false, false);
                                        else
                                            $value = $store->convertPrice($product->getPrice(), false, false);
                                        break;
                                    case ('store_price'):
                                        $value = $store->convertPrice($product->getFinalPrice(), false, false);
                                        break;
                                    case ('parent_url'):
                                        if ($parent_product && $parent_product->getEntityId()) {
                                            $value = $this->getProductUrl($parent_product, $baseUrl);
                                            //                                        $value = $parent_product->getProductUrl(false);
                                        } else {
                                            $value = $this->getProductUrl($product, $baseUrl);
                                        }
                                        break;
                                    case 'parent_base_image':
                                        if ($parent_product && $parent_product->getEntityId() > 0) {
                                            $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                        }
                                        try {
                                            if ($image_width || $image_height) {
                                                $image_url = (string) Mage::helper('catalog/image')->init($_prod, 'image')->resize($image_width, $image_height);
                                            } else {
                                                $image_url = (string) Mage::helper('catalog/image')->init($_prod, 'image');
                                            }
                                        }
                                        catch (Exception $e) {
                                            $image_url = '';
                                        }
                                        $value = $image_url;
                                        break;
                                    case 'parent_description':
                                        $description = '';
                                        if ($parent_product && $parent_product->getEntityId() > 0) {
                                            $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                        }
                                        try {
                                            $description = $_prod->getDescription();
                                        }
                                        catch (Exception $e) {
                                            $description = '';
                                        }
                                        $value = $description;
                                        break;
                                    case 'parent_product_price':
                                        if ($parent_product && $parent_product->getEntityId() > 0) {
                                            $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                        }
                                        try {
                                            $price = $_prod->getResource()->getAttribute('price')->getFrontend()->getValue($_prod);
                                        }
                                        catch (Exception $e) {
                                            $price = '';
                                        }
                                        $value = number_format($price);
                                        break;
                                    case 'parent_product_special_price':
                                        if ($parent_product && $parent_product->getEntityId() > 0) {
                                            $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                        }
                                        try {
                                            $specialprice = $_prod->getResource()->getAttribute('special_price')->getFrontend()->getValue($_prod);
                                        }
                                        catch (Exception $e) {
                                            $specialprice = '';
                                        }
                                        $value = number_format($specialprice);
                                        break;
                                    case 'parent_brand':
                                        $brand = '';
                                        if ($parent_product && $parent_product->getEntityId() > 0) {
                                            $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                            try {
                                                $brandAttr = $_prod->getResource()->getAttribute('brand');
                                                if ($brandAttr) {
                                                    $brand = $brandAttr->getFrontend()->getValue($_prod);
                                                }
                                            }
                                            catch (Exception $e) {
                                                $brand = '';
                                            }
                                        }
                                        $value = $brand;
                                        break;
                                    case 'image_link':
                                        $url = Mage::getBaseUrl('media') . "catalog/product" . $_prod->getImage();
                                        if (!$_prod->getImage()) {
                                            if ($parent_product && $parent_product->getEntityId() > 0) {
                                                $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                                $url   = Mage::getBaseUrl('media') . "catalog/product" . $_prod->getImage();
                                            }
                                        } else {
                                            $url = Mage::getBaseUrl('media') . "catalog/product" . $_prod->getImage();
                                        }
                                        if ($url == Mage::getBaseUrl('media') . "catalog/product" || $url == Mage::getBaseUrl('media') . "catalog/productno_selection") {
                                            $url = Mage::getBaseUrl('media') . "catalog/product/i/m/img-na-450_1.jpg";
                                        }
                                        $value = $url;
                                        break;
                                    case 'parent_name':
                                        if ($parent_product && $parent_product->getEntityId() > 0) {
                                            $_prod = Mage::getModel('catalog/product')->load($parent_product->getId());
                                            $name  = $_prod->getName();
                                        } else {
                                            $name = '';
                                        }
                                        $value = $name;
                                        break;
                                    case ('image'):
                                    case ('gallery'):
                                    case ('media_gallery'):
                                        if (!$product->hasData('product_base_image')) {
                                            try {
                                                if ($image_width || $image_height) {
                                                    $image_url = (string) Mage::helper('catalog/image')->init($_prod, 'image')->resize($image_width, $image_height);
                                                } else {
                                                    $image_url = (string) Mage::helper('catalog/image')->init($_prod, 'image');
                                                }
                                            }
                                            catch (Exception $e) {
                                                $image_url = '';
                                            }
                                            $product->setData('product_base_image', $image_url);
                                            $value = $image_url;
                                        } else {
                                            $value = $product->getData('product_base_image');
                                        }
                                        break;
                                    case ('image_2'):
                                    case ('image_3'):
                                    case ('image_4'):
                                    case ('image_5'):
                                        if (!$product->hasData('media_gallery_images')) {
                                            $product->setData('media_gallery_images', $_prod->getMediaGalleryImages());
                                        }
                                        $i = 0;
                                        foreach ($product->getMediaGalleryImages() as $_image) {
                                            $i++;
                                            if (('image_' . $i) == $col->attribute_value) {
                                                if ($image_width || $image_height) {
                                                    $value = (string) Mage::helper('catalog/image')->init($product, 'image', $_image->getFile())->resize($image_width, $image_height);
                                                } else {
                                                    $value = $_image['url'];
                                                }
                                            }
                                        }
                                        break;
                                    case ('url'):
                                        $value = $product->getProductUrl($product, $baseUrl);
                                        //                                    $value = $product->getProductUrl();
                                        break;
                                    case ('qty'):
                                        $value = ceil((int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty());
                                        break;
                                    case ('category'):
                                        $value = $product->getCategory();
                                        break;
                                    case ('product_type'):
                                        $value = $product->getTypeId();
                                        break;
                                    case ('is_in_stock'):
                                        //                                    $value = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                                        $value = $product->getData('is_in_stock');
                                        //                                    $value = $product->getData('is_salable');
                                        break;
                                    default:
                                        if ($attribute = $attributes->getItemByColumnValue('attribute_code', $col->attribute_value)) {
                                            if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                                                $value = implode(', ', (array) $product->getAttributeText($col->attribute_value));
                                            } else {
                                                $value = $product->getData($col->attribute_value);
                                            }
                                        } else {
                                            $value = $product->getData($col->attribute_value);
                                        }
                                        break;
                                }
                            } else {
                                $value = '';
                            }
                            $fields[] = $value;
                        }
                        if ($enclosure != ' ') {
                            fputcsv($fp, $fields, $delimiter, $enclosure);
                        } else {
                            $this->myfputcsv($fp, $fields, $delimiter);
                        }
                        // only simple can be unset or we will lose the parents
                        if ($product->getTypeId() == 'simple') {
                            $product->clearInstance();
                        }
                    }
                    
                    if ($csv_data = @file_get_contents($feed->getTempFilePath())) {
                        fwrite($fp, $csv_data);
                    }
                    if (file_exists($feed->getTempFilePath())) {
                        unlink($feed->getTempFilePath());
                    }
                    fclose($fp);
                }
                
                Mage::app()->setCurrentStore($feed->getStoreId());
                $feed->setRestartCron(1);
				$feed->setGeneratedAt(Mage::getSingleton('core/date')->gmtDate());
                $feed->save();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('core')->__('Your feed is generated. You can download in feed profile.'));
                
            }
            catch (Mage_Core_Exception $e) {
                
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                
            }
            catch (Exception $e) {
            
                if (!ini_get('allow_url_fopen')) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Check the "allow_url_fopen" option.
                            Check that the "allow_url_fopen" option it enabled.
                            This option enables the URL-aware fopen wrappers that enable accessing URL object like files.
                            Learn more at <a target="_blank" href="http://php.net/manual/en/filesystem.configuration.php">http://php.net/manual/en/filesystem.configuration.php</a>'));
                } elseif (strpos(strtolower($e->getMessage()), 'permission')) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Check the Permission for the "Media" directory.
							Check that the "media" directory of your Magento has permission equal to 777 or 0777.'));
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Can\'t generate feed file'));
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('If "Time out" error.
							 Please ask your server administrator to increase script run times. Learn more at <a target="_blank" href="http://php.net/manual/en/function.set-time-limit.php">http://php.net/manual/en/function.set-time-limit.php</a>'));
                }
            }
            
            return $this->_redirect('*/*/');
            
        }
        
        return $this->_redirect('*/*/index');
        
    }
    
    public function getattributevaluefieldAction()
    {
        
        if ($code = $this->getRequest()->getParam('attribute_code')) {
            
            $name     = $this->getRequest()->getParam('element_name');
            $store_id = $this->getRequest()->getParam('store_id');
            $iterator = $this->getRequest()->getParam('iterator');
            
            if ($code == 'product_type') {
                $condition = CP_Feed_Block_Adminhtml_Items_Edit_Tab_Filter::getConditionSelectLight($iterator);
            } else {
                $condition = CP_Feed_Block_Adminhtml_Items_Edit_Tab_Filter::getConditionSelect($iterator);
            }
            
            $this->getResponse()->setBody(Zend_Json::encode(array(
                'attributevalue' => CP_Feed_Block_Adminhtml_Items_Edit_Tab_Filter::getAttributeValueField($code, $name, null, $store_id),
                'condition' => $condition,
                'iterator' => $iterator
            )));
        }
        
    }
    
}
