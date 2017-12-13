<?php

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'background_colour', array(
    'group'         =>  "General Information",
    'type'          =>  'text',
    'label'         =>  'Background Colour',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
));

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'left_background_image', array(
    'group'         => 'General Information',
    'input'         => 'image',
    'type'          => 'varchar',
    'label'         => 'Left Background Image',
    'backend'       => 'catalog/category_attribute_backend_image',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'used_in_product_listing' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'right_background_image', array(
    'group'         => 'General Information',
    'input'         => 'image',
    'type'          => 'varchar',
    'label'         => 'Right Background Image',
    'backend'       => 'catalog/category_attribute_backend_image',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'used_in_product_listing' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();
