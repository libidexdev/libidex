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

class Magegiant_GiftCard_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
	public function getProductThumbnail()
	{
		$container = new Varien_Object(array(
			'block' => $this
		));

		Mage::dispatchEvent('giftcard_item_renderer_product_thumbnail', array(
			'container' => $container
		));

		if ($thumbnail = $container->getThumbnail()) {
			return $thumbnail;
		}

		return parent::getProductThumbnail();
	}

	public function getProductName()
	{
		$container = new Varien_Object(array(
			'block' => $this
		));

		Mage::dispatchEvent('giftcard_item_renderer_product_name', array(
			'container' => $container
		));

		if ($productName = $container->getProductName()) {
			return $productName;
		}

		return $this->getProduct()->getName();
	}

	public function getOptionList()
	{
		$options     = parent::getProductOptions();
		$allowOption = explode(',', Mage::helper('giftcard')->getConfig('gift_code/item_options'));
		$optionLabel = Mage::getModel('giftcard/source_options')->toOptionHash();
		$helper      = Mage::helper('giftcard/item_renderer');
		$giftCard    = $this->getItem()->getBuyRequest()->getGiftcard();

		$helper->groupField($giftCard, array('recipient', 'sender'));

		foreach ($giftCard as $code => $value) {
			if (in_array($code, $allowOption) && $value) {
				$options[] = array(
					'label' => $optionLabel[$code],
					'value' => $helper->getOptionValue($code, $value)
				);
			}
		}

		$container = new Varien_Object(array(
			'options'  => $options,
			'renderer' => $this,
			'request'  => $giftCard
		));
		Mage::dispatchEvent('giftcard_item_renderer_get_option_list', array(
			'container' => $container
		));

		return $container->getOptions();
	}
}
