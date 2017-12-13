<?php
$this->startSetup();

	$this->addAttribute('catalog_category', 'custom_cat_bg_img', array(
	    'group'         => 'General Information',
	    'type'   		=> 'varchar',
		'input'   		=> 'image',
		'backend' 		=> 'catalog/category_attribute_backend_image',
	    'label'         => 'Category background image',
		'note' 			=> 'Full width background image',
		'visible' 		=> true,
		'required' 		=> false,
		'user_defined' 	=> false,
		'visible_on_front' => true,
		'used_in_product_listing' => false,
		'is_html_allowed_on_front' => false,
	    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
	
	
	
	$this->addAttribute('catalog_category', 'custom_cat_bg_text_colour', array(
		'group'         =>  "General Information",
	    'type'          =>  'text',
	    'label'         =>  'Text Colour eg(#ffffff)',
	    'input'         =>  'text',
	    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	    'visible'       =>  true,
	    'required'      =>  false,
	    'user_defined'  =>  true,
	    'default'       =>  "",
	    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
  
$this->endSetup();