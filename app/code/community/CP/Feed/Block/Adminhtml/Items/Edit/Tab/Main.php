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

class Cp_Feed_Block_Adminhtml_Items_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
    
    
    
    
    
    protected function _prepareForm()
    {
        
        
        
        $form = new Varien_Data_Form();
        
        
        
        if (Mage::registry('cp_feed')) {
            
            $item = Mage::registry('cp_feed');
            
        } else {
            
            $item = new Varien_Object();
            
        }
        
        
        
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('main_fieldset', array(
            'legend' => $this->__('Item information')
        ));
        
        
        $fieldset->addField('type', 'hidden', array(
            
            'name' => 'type'
            
        ));
        
        
        
        $fieldset->addField('name', 'text', array(
            
            'name' => 'name',
            
            'label' => $this->__('Name'),
            
            'title' => $this->__('Name'),
            
            'required' => true
            
        ));
        
        if ($item->getId() && ($url = $item->getUrl())) {
            
            
            
            $fieldset->addField('comments', 'note', array(
                
                'label' => $this->__('Access Url'),
                
                'title' => $this->__('Access Url'),
                
                'text' => '<a href="' . $url . '" target="_blank">' . $url . '</a>'
                
            ));
            
        }
        
        
        
        $fieldset->addField('filename', 'text', array(
            
            'name' => 'filename',
            
            'label' => $this->__('Filename'),
            
            'title' => $this->__('Filename'),
            
            'required' => false
            
        ));
        
        
        
        $fieldset->addField('store_id', 'select', array(
            
            'label' => $this->__('Store View'),
            
            'required' => true,
            
            'name' => 'store_id',
            
            'values' => Mage::getModel('cp_feed/adminhtml_system_config_source_store')->getStoreValuesForForm()
            
        ));
        
        
        
        if (!$item->getType() && $this->getRequest()->getParam('type')) {
            
            $item->setType($this->getRequest()->getParam('type'));
            
        }
        
        
        
        $form->setValues($item->getData());
        
        
        
        
        
        return parent::_prepareForm();
        
        
        
    }
    
    
    
    
    
}