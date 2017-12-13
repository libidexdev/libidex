<?php
$this->startSetup();
$this->addAttribute('catalog_category', 'custom_cat_image_4', array(
  			'type' => 'varchar',
            'label' => 'Category background image',
            'note' => 'Make sure the width is 260px',
            'input' => 'image',
            'backend' => 'catalog/category_attribute_backend_image',
            'required' => false,
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'visible_on_front' => true,
            'used_in_product_listing' => false,
            'is_html_allowed_on_front' => false,
            'group'         => 'General Information',
));
 
$this->endSetup();