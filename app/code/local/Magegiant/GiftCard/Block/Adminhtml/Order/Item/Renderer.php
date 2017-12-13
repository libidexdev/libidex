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

class Magegiant_GiftCard_Block_Adminhtml_Order_Item_Renderer
	extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
	public function getOrderOptions()
	{
		$options  = array();
		$item     = $this->getItem();
		$giftCard = $item->getBuyRequest()->getGiftcard();
		if ($giftCard) {
			$allowOption = explode(',', Mage::helper('giftcard')->getConfig('gift_code/item_options'));
			$optionLabel = Mage::getModel('giftcard/source_options')->toOptionHash();
			$helper      = Mage::helper('giftcard/item_renderer');

			$helper->groupField($giftCard, array('recipient', 'sender'));

			foreach ($giftCard as $code => $value) {
				if (in_array($code, $allowOption) && $value) {
					$options[] = array(
						'label' => $optionLabel[$code],
						'value' => $helper->getOptionValue($code, $value)
					);
				}
			}

			$codeList = Mage::getModel('giftcard/giftcard')->loadByItemId($item->getId())
				->getColumnValues('code');
			if ($numOfCode = sizeof($codeList)) {
				$options[] = array(
					'label' => ($numOfCode > 1) ? Mage::helper('giftcard')->__('Gift Codes') : Mage::helper('giftcard')->__('Gift Code'),
					'value' => implode(', ', $codeList)
				);
			}

			$product = $item->getProduct();
			if ($product) {
				$type      = $product->getGiftcardProductType();
				array_unshift($options, array(
					'label' => Mage::helper('giftcard')->__('Type'),
					'value' => Mage::getModel('giftcard/source_product_type')->getOptionLabel($type)
				));
			}

			$container = new Varien_Object(array(
				'options'  => $options,
				'renderer' => $this,
				'request'  => $giftCard
			));
			Mage::dispatchEvent('giftcard_adminhtml_order_item_renderer_get_option_list', array(
				'container' => $container
			));
			$options = $container->getOptions();
		}

		return array_merge($options, parent::getOrderOptions());
	}
}
