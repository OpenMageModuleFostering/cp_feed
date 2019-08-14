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

class CP_Feed_Block_Adminhtml_Items_Edit_Tab_Type extends Mage_Adminhtml_Block_Widget_Form
{
    
    
    
    protected function _toHtml()
    {
        
        
        
        return parent::_toHtml() . '

			<script type="text/javascript">

			var templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;

			function setSettings(urlTemplate, typeElement) {

		        var template = new Template(urlTemplate, templateSyntax);

		        

		        setLocation(template.evaluate({type:$F(typeElement)}));

		    }

			</script>';
        
        
        
    }
    
    
    
    protected function _prepareLayout()
    {
        
        $this->setChild('continue_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            
            'label' => $this->__('Continue'),
            
            'onclick' => "setSettings('" . $this->getContinueUrl() . "','type')",
            
            'class' => 'save'
            
        )));
        
        return parent::_prepareLayout();
        
    }
    
    
    
    public function getContinueUrl()
    {
        
        return $this->getUrl('*/*/new', array(
            
            '_current' => true,
            
            'type' => '{{type}}'
            
        ));
        
    }
    
    
    
    protected function _prepareForm()
    {
        
        
        
        $form = new Varien_Data_Form();
        
        
        
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('main_fieldset', array(
            'legend' => $this->__('Settings')
        ));
        
        
        
        $fieldset->addField('type', 'select', array(
            
            'name' => 'type',
            
            'label' => $this->__('Feed Type'),
            
            'title' => $this->__('Feed Type'),
            
            'required' => true,
            
            'values' => array(
                'google' => 'Google Shopping',
                /*'amazon' => 'Amazon Marketplace',
                'other' => 'CPC Strategy'*/
            )
            
        ));
        
        
        $fieldset->addField('continue_button', 'note', array(
            
            'text' => $this->getChildHtml('continue_button')
            
        ));
        
        return parent::_prepareForm();
        
        
        
    }
    
    
    
    
    
}