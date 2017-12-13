<?php
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', "old_product_id", array(
    'group'             => 'General',
    'type'              => 'varchar',
    'input'             => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Old Product ID',
    'sort_order'        => 1000,
    'required'          => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
));


$installer->endSetup();