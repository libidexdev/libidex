<?php
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
$installer->startSetup();

$content = 'BLOCK CONTENT HERE';
//if you want one block for each store view, get the store collection
$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('store_id', array('gt'=>0))->getAllIds();
//if you want one general block for all the store viwes, uncomment the line below
foreach ($stores as $store){
    $block = Mage::getModel('cms/block');
    $block->setTitle('Promotion for Daily Deal');
    $block->setIdentifier('promotion_for_daily_deal');
    $block->setStores(array($store));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$installer->endSetup();