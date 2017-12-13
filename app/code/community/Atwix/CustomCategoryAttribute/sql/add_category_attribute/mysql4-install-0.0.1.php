<?php
$this->startSetup();
$this->addAttribute('catalog_category', 'custom_cat_image', array(
    'group'         => 'General Information',
    'type'    => 'varchar',
	'input'   => 'image',
	'backend' => 'catalog/category_attribute_backend_image',
    'label'         => 'Category background image',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
 
$this->endSetup();