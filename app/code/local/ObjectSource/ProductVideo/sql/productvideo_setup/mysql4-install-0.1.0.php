<?php
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
$installer->startSetup();

$this->addAttribute('catalog_product', "vembedded_code", array(
    'type'       => 'varchar',
    'input'      => 'text',
    'label'      => 'Video Embedded Code',
    'sort_order' => 1000,
    'required'   => false,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'backend'    => '',
));

$this->endSetup(); 