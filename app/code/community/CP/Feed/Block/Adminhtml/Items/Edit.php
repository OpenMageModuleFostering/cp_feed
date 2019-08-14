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

class Cp_Feed_Block_Adminhtml_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    
    public function __construct()
    {
        
        
        
        parent::__construct();
        
        
        
        $this->_objectId = 'id';
        
        $this->_blockGroup = 'cp_feed';
        
        $this->_controller = 'adminhtml_items';
        
        
        
        $this->_updateButton('save', 'label', $this->__('Save'));
        
        $this->_updateButton('delete', 'label', $this->__('Delete'));
        
        
        
        $feed = Mage::registry('cp_feed');
        
        
        
        if ($feed && $feed->getId() > 0) {
            
            
            
            $this->_addButton('generate', array(
                
                'label' => $this->__('Generate File'),
                
                'onclick' => 'if($(\'loading-mask\')){$(\'loading-mask\').show();}setLocation(\'' . $this->getUrl('*/*/generate', array(
                    'id' => $feed->getId()
                )) . '\')'
                
            ), -100);
            
            
            
            if ($feed->getFtpActive()) {
                
                
                
                $this->_addButton('upload', array(
                    
                    'label' => $this->__('Upload File'),
                    
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/upload', array(
                        'id' => $feed->getId()
                    )) . '\')'
                    
                ), -100);
                
                
                
            }
            
            
            
        }
        
        
        
        $this->_addButton('saveandcontinue', array(
            
            'label' => $this->__('Save And Continue Edit'),
            
            'onclick' => 'saveAndContinueEdit()',
            
            'class' => 'save'
            
        ), -100);
        
        
        
        $_data = array();
        
        $_data['data'] = Cp_Feed_Block_Adminhtml_Items_Edit_Tab_Content_Csv::getSystemSections();
        
        $_data['url'] = $this->getUrl('*/*/mappingimportsection', array(
            'id' => ($feed && $feed->getId() ? $feed->getId() : 0)
        ));
        
        
        
        $this->_formScripts[] = "

            function saveAndContinueEdit(){

                editForm.submit($('edit_form').action+'back/edit/');

            }

            

            var CpFeedAdmin = new CpFeedAdminSettings(" . Zend_Json::encode($_data) . ");



        ";
        
        
        
        if ($this->getRequest()->getActionName() == 'new' && !$this->getRequest()->getParam('type')) {
            
            $this->removeButton('save');
            
            $this->removeButton('saveandcontinue');
            
        }
        
        
        
    }
    
    
    
    public function getHeaderText()
    {
        
        
        
        if (Mage::registry('cp_feed') && Mage::registry('cp_feed')->getId()) {
            
            return $this->__("Edit %s", $this->htmlEscape(Mage::registry('cp_feed')->getName()));
            
        } else {
            
            return $this->__('Add Item');
            
        }
        
    }
    
} 