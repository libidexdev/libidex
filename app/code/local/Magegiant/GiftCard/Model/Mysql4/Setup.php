<?php

/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */
class Magegiant_GiftCard_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
	public function setApplyToAttribute($setup)
	{
		$attAdd = array('weight', 'tax_class_id');
		foreach ($attAdd as $code) {
			$applyTo = $setup->getAttribute('catalog_product', $code, 'apply_to');
			if ($applyTo) {
				$applyTo = explode(',', $applyTo);
				if (!in_array(Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE, $applyTo)) {
					$applyTo[] = Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE;
					$setup->updateAttribute('catalog_product', $code, 'apply_to', join(',', $applyTo));
				}
			}
		}

		$applyTo = explode(',', $setup->getAttribute('catalog_product', 'cost', 'apply_to'));
		if (in_array(Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE, $applyTo)) {
			foreach ($applyTo as $k => $v) {
				if ($v == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
					unset($applyTo[$k]);
					break;
				}
			}
			$setup->updateAttribute('catalog_product', 'cost', 'apply_to', join(',', $applyTo));
		}

		return $this;
	}

	public function addGiftCardAttribute()
	{
		$setup = Mage::getResourceModel('catalog/setup', 'giant_giftcard_setup');

		$entityTypeId  = $setup->getEntityTypeId('catalog_product');
		$attributeSets = $setup->_conn->fetchAll('select * from ' . $this->getTable('eav/attribute_set') . ' where entity_type_id=?', $entityTypeId);
		foreach ($attributeSets as $attributeSet) {
			$setup->addAttributeGroup($entityTypeId, $attributeSet['attribute_set_id'], 'Gift Card Information', 2);
		}

		$attributes = $this->getAttributeArray();
		foreach ($attributes as $code => $data) {
			$this->addAttribute($setup, $code, $data);
		}

		$this->setApplyToAttribute($setup);

		return $this;
	}

	public function addAttribute($setup, $field, $data)
	{
		$attArray = $this->getDefaultAttributeArray();

		$setup->removeAttribute('catalog_product', $field);
		$setup->addAttribute('catalog_product', $field, array_merge($attArray, $data));

		return $this;
	}

	public function getAttributeArray()
	{
		return array(
			'giftcard_amount_type'           => array(
				'group'      => 'Prices',
				'type'       => 'int',
				'label'      => 'Gift Card Value Type',
				'input'      => 'select',
				'source'     => 'giftcard/source_type_amount',
				'sort_order' => -5,
			),
			'giftcard_amount'                => array(
				'group'      => 'Prices',
				'type'       => 'text',
				'label'      => 'Amounts & Prices',
				'required'   => true,
				'sort_order' => -4,
			),
			'giftcard_amount_from'           => array(
				'group'      => 'Prices',
				'backend'    => 'catalog/product_attribute_backend_price',
				'label'      => 'Gift Card Amount Min Value',
				'class'      => 'validate-number',
				'required'   => true,
				'sort_order' => -3,
			),
			'giftcard_amount_to'             => array(
				'group'      => 'Prices',
				'backend'    => 'catalog/product_attribute_backend_price',
				'label'      => 'Gift Card Amount Max Value',
				'class'      => 'validate-number',
				'required'   => true,
				'sort_order' => -2,
			),
			'giftcard_price_percent'         => array(
				'group'      => 'Prices',
				'type'       => 'decimal',
				'label'      => 'Gift Card Price Percent',
				'input'      => 'text',
				'default'    => '100',
				'note'       => '%',
				'required'   => true,
				'sort_order' => -1,
			),
			'giftcard_product_type'          => array(
				'type'       => 'int',
				'label'      => 'Gift Card Type',
				'input'      => 'select',
				'source'     => 'giftcard/source_product_type',
				'sort_order' => 25,
			),
			'pattern'                        => array(
				'type'           => 'text',
				'label'          => 'Code Pattern',
				'input'          => 'text',
				'required'       => true,
				'sort_order'     => 27,
				'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'input_renderer' => 'giftcard/adminhtml_catalog_product_renderer_field'
			),
			'use_config_pattern'             => array(
				'type'       => 'int',
				'label'      => 'Use Config Pattern',
				'input'      => 'text',
				'visible'    => false,
				'sort_order' => 28,
			),
			'expire'                         => array(
				'type'           => 'int',
				'label'          => 'Expired After',
				'input'          => 'text',
				'sort_order'     => 30,
				'required'       => true,
				'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'input_renderer' => 'giftcard/adminhtml_catalog_product_renderer_field'
			),
			'use_config_expire'              => array(
				'type'       => 'int',
				'label'      => 'Use Config Expire',
				'input'      => 'text',
				'visible'    => false,
				'sort_order' => 35,
			),
			'use_message'                    => array(
				'type'           => 'int',
				'label'          => 'Use Message',
				'input'          => 'select',
				'source'         => 'giftcard/source_yesno',
				'required'       => true,
				'sort_order'     => 40,
				'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'input_renderer' => 'giftcard/adminhtml_catalog_product_renderer_field'
			),
			'use_config_use_message'         => array(
				'type'       => 'int',
				'label'      => 'Use Config Use Message',
				'input'      => 'text',
				'visible'    => false,
				'sort_order' => 45,
			),
			'schedule_enable'                => array(
				'type'           => 'int',
				'label'          => 'Enable Scheduling Delivery',
				'input'          => 'select',
				'source'         => 'giftcard/source_yesno',
				'required'       => true,
				'sort_order'     => 50,
				'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'input_renderer' => 'giftcard/adminhtml_catalog_product_renderer_field'
			),
			'use_config_schedule_enable'     => array(
				'type'       => 'int',
				'label'      => 'Use Config Enable Scheduling Delivery',
				'input'      => 'text',
				'visible'    => false,
				'sort_order' => 55,
			),
			'giftcard_conditions_serialized' => array(
				'type'    => 'text',
				'label'   => 'Conditions',
				'input'   => 'text',
				'visible' => false,
			),
			'giftcard_actions_serialized'    => array(
				'type'    => 'text',
				'label'   => 'Actions',
				'input'   => 'text',
				'visible' => false,
			),
			'conditions_description'         => array(
				'type'       => 'text',
				'label'      => 'Conditions/Actions Description',
				'input'      => 'textarea',
				'sort_order' => 100,
				'note'       => 'This description will show on shopping cart page if customer apply gift card which don\'t satisfy conditions',
				'default'    => 'This code doesn\'t satisfy to apply for this order'
			)
		);
	}

	public function getDefaultAttributeArray()
	{
		return array(
			'group'                   => 'Gift Card Information',
			'type'                    => 'decimal',
			'backend'                 => '',
			'frontend'                => '',
			'label'                   => '',
			'input'                   => 'price',
			'class'                   => 'general',
			'source'                  => '',
			'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
			'visible'                 => true,
			'required'                => false,
			'user_defined'            => true,
			'default'                 => '',
			'searchable'              => false,
			'filterable'              => false,
			'comparable'              => false,
			'visible_on_front'        => false,
			'unique'                  => false,
			'apply_to'                => Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE,
			'is_configurable'         => false,
			'used_in_product_listing' => true,
			'sort_order'              => 0,
		);
	}
}
