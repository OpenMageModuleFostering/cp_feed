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

class CP_Feed_Block_Adminhtml_Items_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        
        
        
        parent::__construct();
        
        $this->setId('cp_feed_tabs');
        
        $this->setDestElementId('edit_form');
        
        $this->setTitle($this->__('Feed Information'));
        
        
        
    }
    
    
    
    protected function _prepareLayout()
    {
        
        if (($type = $this->getRequest()->getParam('type', null)) || (Mage::registry('cp_feed') && ($type = Mage::registry('cp_feed')->getType()))) {
            
            
            $this->addTab('main_section', array(
                
                'label' => $this->__('Item information'),
                
                'title' => $this->__('Item information'),
                
                'content' => $this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tab_main')->toHtml()
                
            ));
            
            $this->addTab('content_section', array(
                
                'label' => $this->__('Content Settings'),
                
                'title' => $this->__('Content Settings'),
                
                'content' => $this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tab_content_csv')->setTemplate('cp/feed/item/edit/content.phtml')->toHtml()
                
            ));
            
            
            $this->addTab('advanced', array(
                
                'label' => $this->__('Advanced Settings'),
                
                'title' => $this->__('Advanced Settings'),
                
                'content' => $this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tab_advanced')->toHtml()
                
            ));
            
            $this->addTab('product_category', array(
                
                'label' => $this->__('Product categories'),
                
                'title' => $this->__('Product categories'),
                
                'content' => $this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tab_content_productcategory')->toHtml()
                
                //->setTemplate('cp/feed/item/edit/category_product.phtml')->toHtml(),
                
            ));
            
        } else {
            
            
            
            $this->addTab('main_section', array(
                
                'label' => $this->__('Content Settings'),
                
                'title' => $this->__('Content Settings'),
                
                'content' => $this->getLayout()->createBlock('cp_feed/adminhtml_items_edit_tab_type')->toHtml()
                
            ));
            
            
            
        }
        
        if ($tabId = addslashes(htmlspecialchars($this->getRequest()->getParam('tab')))) {
            
            
            
            $this->setActiveTab($tabId);
            
        }
        
        
        
        
        
        return parent::_beforeToHtml();
        
        
        
    }
    
    
    
}