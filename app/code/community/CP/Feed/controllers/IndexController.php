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

class CP_Feed_IndexController extends Mage_Core_Controller_Front_Action
{
    
    public function indexAction()
    {
        
        $response = array(
            'result' => 0
        );
        
        if ($feed_id = $this->getRequest()->getParam('id')) {
            
            $feed   = Mage::getModel('cp_feed/item')->load($feed_id);
            $start  = intval($this->getRequest()->getParam('start'));
            $length = intval($this->getRequest()->getParam('length'));
            
            
            if ($start >= 0 && $length >= 0) {
                
                if ($feed->getType() == 'csv') {
                    
                    $feed->writeTempFile($start, $length);
                    
                } else {
                    
                    Mage::getModel('cp_feed/item_block_product', array(
                        'feed' => $feed,
                        'content' => ''
                    ))->writeTempFile($start, $length);
                    
                }
                
                $response['result'] = 1;
                
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($response));
        
    }
    
}