<?php
$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'discount_active', array(
    'group'         => 'General Information',
    'type'          => 'int',
    'input'         => 'select',
    'source'   => 'eav/entity_attribute_source_boolean',
    'label'         => 'Discount Active?',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'default'           => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'custom_corner_image', array(
    'group'         => 'General Information',
    'type'          => 'varchar',
    'input'         => 'image',
    'backend'       => 'catalog/category_attribute_backend_image',
    'label'         => 'Discount Corner Image',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$this->endSetup();