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
 
class CP_Feed_Model_Observer extends Mage_Cron_Model_Observer
{
    protected function _isWrongTimeStamp(){
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $write->query("SELECT vartimestamp FROM cp_feed WHERE id=1");
        /** @var  $result Varien_Db_Statement_Pdo_Mysql */
        $row = $result->fetchAll();
        $prev_timestamp = $row[0]['vartimestamp'] + (60 * 4);

       return ($prev_timestamp > time());
    }
	
	/* public function proccessFeedsTest()
    {
		
		$saveTestimonial = Mage::getModel('testimonial/testimonial');
		$saveTestimonial->setName('CP_Feed_Model_Observer');
		$saveTestimonial->save();
		
		$msg = "First line of text\nSecond line1414 of text";
		$msg = wordwrap($msg,70);
		mail("commerce.pundit61@gmail.com","My subject",$msg);
	} */
	
    public function proccessFeeds()
    {
		
		if ($this->_isWrongTimeStamp()) {
            // Handle the exit
            return;
        }
		
		
        $collection = Mage::getResourceModel('cp_feed/item_collection')
            //->addFieldToFilter('restart_cron', '1')
            ->addFieldToFilter('upload_day', array(
                'like' => '%' . strtolower(date('D')) . '%'
            )); 
		//echo count($collection);
		//exit; 

	
        foreach ($collection as $feed) {
            try {
                /** @var $feed CP_Feed_Model_Item */
                $feed->generateFeed();
                $feed->clearInstance();
                unset($feed);
                //  }
            } catch (Exception $e) {
                $feed->setData('restart_cron', intval($feed->getData('restart_cron')) + 1);
                $feed->save();
                continue;
            }
        }
		
		
    }

    static function generateAll()
    {
        $collection = Mage::getResourceModel('cp_feed / item_collection');
        foreach ($collection as $feed) {
            try {
                Mage::app()->setCurrentStore($feed->getStoreId());
                $feed->generate();
            } catch (Exception $e) {
                continue;
            }
        }
    }

}
