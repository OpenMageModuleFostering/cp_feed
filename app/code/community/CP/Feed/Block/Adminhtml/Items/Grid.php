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

class Cp_Feed_Block_Adminhtml_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    public function __construct()
    {
        
        parent::__construct();
        
        $this->setId('cpfeedsGrid');
        
        $this->setDefaultSort('date');
        
        $this->setDefaultDir('DESC');
        
        $this->setSaveParametersInSession(true);
        
    }
    
    
    
    protected function _prepareCollection()
    {
        
        $collection = Mage::getModel('cp_feed/item')->getCollection();
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
        
    }
    
    
    
    protected function _prepareColumns()
    {
        
        $this->addColumn('id', array(
            
            'header' => $this->__('ID'),
            
            'align' => 'left',
            
            'index' => 'id',
            
            'type' => 'number',
            
            'width' => '50px'
            
        ));
        
        $this->addColumn('name', array(
            
            'header' => $this->__('Name'),
            
            'align' => 'left',
            
            'index' => 'name'
            
        ));
        
        $this->addColumn('filename', array(
            
            'header' => $this->__('Access Url'),
            
            'align' => 'left',
            
            'index' => 'filename',
            
            'renderer' => 'Cp_Feed_Block_Adminhtml_Items_Grid_Renderer_AccessUrl'
            
        ));
        
        $this->addColumn('last_generated', array(
            
            'header' => $this->__('Last Generated'),
            
            'align' => 'left',
            
            'index' => 'generated_at',
            
            'type' => 'datetime',
            
            'renderer' => 'Cp_Feed_Block_Adminhtml_Items_Grid_Renderer_Datetime'
            
        ));
        
        $this->addColumn('store_id', array(
            
            'header' => $this->__('Store View'),
            
            'align' => 'left',
            
            'index' => 'store_id',
            
            'type' => 'store'
            
        ));
        
        
        $this->addColumn('action', array(
            
            'header' => $this->__('Action'),
            
            'width' => '100',
            
            'type' => 'action',
            
            'getter' => 'getId',
            
            'actions' => array(
                
                array(
                    
                    'caption' => $this->__('Edit'),
                    
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    
                    'field' => 'id'
                    
                )
                
            ),
            
            'filter' => false,
            
            'sortable' => false,
            
            'index' => 'stores',
            
            'is_system' => true
            
        ));
        
        
        
        return parent::_prepareColumns();
        
        
        
    }
    
    
    
    protected function _prepareMassaction()
    {
        
        
        
        $this->setMassactionIdField('id');
        
        $this->getMassactionBlock()->setFormFieldName('id');
        
        
        
        $this->getMassactionBlock()->addItem('delete', array(
            
            'label' => $this->__('Delete Feed(s)'),
            
            'url' => $this->getUrl('*/*/massDelete'),
            
            'confirm' => $this->__('Are you sure?')
            
        ));
        
        
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    protected function _afterLoadCollection()
    {
        
        
        
        $this->getCollection()->walk('afterLoad');
        
        parent::_afterLoadCollection();
        
        
        
    }
    
    
    
    protected function _filterStoreCondition($collection, $column)
    {
        
        
        
        if (!$value = $column->getFilter()->getValue()) {
            
            return;
            
        }
        
        
        
        $this->getCollection()->addStoreFilter($value);
        
        
        
    }
    
    
    
    public function getRowUrl($row)
    {
        
        
        
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId()
        ));
        
        
        
    }
    
    
    
} 