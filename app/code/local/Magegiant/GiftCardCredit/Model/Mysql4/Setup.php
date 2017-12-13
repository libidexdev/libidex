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
class Magegiant_GiftCardCredit_Model_Mysql4_Setup extends Magegiant_GiftCard_Model_Mysql4_Setup
{
	public function addGiftCardAttribute()
	{
		$setup = Mage::getResourceModel('catalog/setup', 'giant_giftcard_setup');

		$attArray = $this->getDefaultAttributeArray();
		foreach ($this->getAttributeArray() as $code => $data) {
			$setup->removeAttribute('catalog_product', $code);
			$setup->addAttribute('catalog_product', $code, array_merge($attArray, $data));
		}

		return $this;
	}

	public function getAttributeArray()
	{
		return array(
			'allow_redeem'            => array(
				'type'           => 'int',
				'label'          => 'Allow Redeem to Customer Balance',
				'input'          => 'select',
				'source'         => 'giftcard/source_yesno',
				'required'       => true,
				'sort_order'     => 60,
				'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'input_renderer' => 'giftcard/adminhtml_catalog_product_renderer_field'
			),
			'use_config_allow_redeem' => array(
				'type'       => 'int',
				'label'      => 'Use Config Allow Redeem Gift Card',
				'input'      => 'text',
				'visible'    => false,
				'sort_order' => 65,
			)
		);
	}
}
