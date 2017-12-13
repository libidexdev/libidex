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

class Magegiant_GiftCard_Model_Catalog_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
	protected $_canConfigure = true;
	protected $_canUseQtyDecimals = false;

	protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
	{
		$result = parent::_prepareProduct($buyRequest, $product, $processMode);
		if (is_string($result)) {
			return $result;
		}

		try {
			$priceArray = $this->_preparePrice($product, $buyRequest);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		$product->addCustomOption('giftcard_price', $priceArray['base_price'], $product);
		$product->addCustomOption('giftcard_amount', $priceArray['base_amount'], $product);

		return $result;
	}

	private function _preparePrice($product, Varien_Object $buyRequest)
	{
		$product        = $this->getProduct($product);
		$giftCardAmount = $product->getPriceModel()->analyzeAmount($product);
		$giftCardData   = $buyRequest->getGiftcard();

		$amount = $giftCardData['amount'];
		$price  = $product->getPrice();
		if ($amount <= 0) {
			$errorMsg = Mage::helper('giftcard')->__('Please specify Gift Card amount.');
		} else {
			$errorMsg = '';
			switch ($giftCardAmount['type']) {
				case 'fixed':
					if ($amount != $giftCardAmount['amount']) {
						$errorMsg = Mage::helper('giftcard')->__('Gift Card amount is invalid.');
					} else {
						$price = $giftCardAmount['price'];
					}
					break;
				case 'dropdown':
					$dropdownAmount = $giftCardAmount['amount'];
					if (!in_array($amount, $dropdownAmount)) {
						$errorMsg = Mage::helper('giftcard')->__('Gift Card amount is invalid.');
					} else {
						foreach ($dropdownAmount as $key => $amt) {
							if ($amount == $amt) {
								$price = $giftCardAmount['price'][$key];
								break;
							}
						}
					}
					break;
				default:
					$minAmount = $giftCardAmount['amount']['from'];
					$maxAmount = $giftCardAmount['amount']['to'];

					$rate = Mage::app()->getStore()->getCurrentCurrencyRate();
					if ($rate != 1) {
						if ($amount) {
							$amount = Mage::app()->getLocale()->getNumber($amount);
							if (is_numeric($amount) && $amount) {
								$amount = Mage::app()->getStore()->roundPrice($amount / $rate);
							}
						}
					}
					$price = $amount * $giftCardAmount['price'] / 100;

					if ($amount < $minAmount) {
						$errorMsg = Mage::helper('giftcard')->__('Gift Card min amount is %s', Mage::helper('core')->currency($minAmount, true, false));
					}

					if ($amount > $maxAmount) {
						$errorMsg = Mage::helper('giftcard')->__('Gift Card max amount is %s', Mage::helper('core')->currency($maxAmount, true, false));
					}

					break;
			}
		}
		if ($errorMsg) {
			Mage::throwException($errorMsg);
		}

		return array('base_amount' => $amount, 'base_price' => $price);
	}

	public function isPhysical($product = null)
	{
		if ($this->getProduct($product)->getGiftcardProductType() == Magegiant_GiftCard_Model_Giftcard::TYPE_PHYSICAL) {
			return true;
		}

		return false;
	}

	public function isVirtual($product = null)
	{
		if ($this->getProduct($product)->getGiftcardProductType() == Magegiant_GiftCard_Model_Giftcard::TYPE_VIRTUAL) {
			return true;
		}

		return false;
	}

	public function hasOptions($product = null)
	{
		return false;
	}

	public function hasRequiredOptions($product = null)
	{
		return true;
	}

	public function checkProductBuyState($product = null)
	{
		parent::checkProductBuyState($product);

		$product = $this->getProduct($product);
		$option  = $product->getCustomOption('info_buyRequest');
		if ($option instanceof Mage_Sales_Model_Quote_Item_Option) {
			$buyRequest = new Varien_Object(unserialize($option->getValue()));

			$this->_preparePrice($product, $buyRequest);

			if (is_array($buyRequest->getGiftcard())) {
				$giftCard = new Varien_Object($buyRequest->getGiftcard());
			} else {
				Mage::throwException(
					Mage::helper('giftcard')->__('Please specify all the required information.')
				);

				return $this;
			}

//			if (!$giftCard->getSenderName()) {
//				Mage::throwException(
//					Mage::helper('giftcard')->__('Please specify sender name.')
//				);
//			}
//			if (!$this->isPhysical($product) && !$giftCard->getSenderEmail()) {
//				Mage::throwException(
//					Mage::helper('giftcard')->__('Please specify sender email.')
//				);
//			}

			if (!$giftCard->getRecipientName()) {
				Mage::throwException(
					Mage::helper('giftcard')->__('Please specify recipient name.')
				);
			}
			if (!$this->isPhysical($product) && !$giftCard->getRecipientEmail()) {
				Mage::throwException(
					Mage::helper('giftcard')->__('Please specify recipient email.')
				);
			}
		}

		return $this;
	}

	public function processBuyRequest($product, $buyRequest)
	{
		$options = array(
			'giftcard' => new Varien_Object($buyRequest->getGiftcard())
		);

		return $options;
	}

	public function getOrderOptions($product = null)
	{
		$optionArr = parent::getOrderOptions($product);

		if ($amount = $this->getProduct($product)->getCustomOption('giftcard_amount')) {
			$buyRequest = $optionArr['info_buyRequest'];
			if (isset($buyRequest['giftcard'])) {
				$giftcard                    = $buyRequest['giftcard'];
				$giftcard['giftcard_amount'] = $amount->getValue();
				$buyRequest['giftcard']      = $giftcard;
			}
			$optionArr['info_buyRequest'] = $buyRequest;
		}

		return $optionArr;
	}
}
