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

class Magegiant_GiftCard_Model_Catalog_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
	public function analyzeAmount($product)
	{
		switch ($product->getGiftcardAmountType()) {
			case Magegiant_GiftCard_Model_Source_Type_Amount::AMOUNT_TYPE_DROPDOWN:
				$amount = $product->getGiftcardAmount();
				if (!is_array($amount)) {
					$amount = unserialize($amount);
				}
				if (sizeof($amount) == 1) {
					$amount  = array_shift($amount);
					$amounts = array(
						'type'   => 'fixed',
						'amount' => $this->roundAmount($amount['amount']),
						'price'  => $this->roundAmount($amount['price'])
					);
				} else {
					$amountArray = array();
					$priceArray  = array();
					foreach ($amount as $key => $amt) {
						$amountArray[$key] = $this->roundAmount($amt['amount']);
						$priceArray[$key]  = $this->roundAmount($amt['price']);
					}
					$amounts = array(
						'type'   => 'dropdown',
						'amount' => $amountArray,
						'price'  => $priceArray
					);
				}
				break;
			case Magegiant_GiftCard_Model_Source_Type_Amount::AMOUNT_TYPE_RANGE:
				$amounts = array(
					'type'   => 'range',
					'amount' => array(
						'from' => $this->roundAmount($product->getGiftcardAmountFrom()),
						'to'   => $this->roundAmount($product->getGiftcardAmountTo())
					),
					'price'  => max(0, min(100, $product->getGiftcardPricePercent()))
				);
				break;
			default:
				$amounts = array(
					'type'   => 'range',
					'amount' => array(
						'from' => 0,
						'to'   => 0
					),
					'price'  => 100
				);
		}

		return $amounts;
	}

	public function getPrice($product)
	{
		$price = $this->getTotalPrices($product, 'min');

		return $price;
	}

	public function getFinalPrice($qty = null, $product)
	{
		$finalPrice = $this->getPrice($product);
		$product->setFinalPrice($finalPrice);

		$finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
		$finalPrice = max(0, $finalPrice);
		$product->setFinalPrice($finalPrice);

		return $finalPrice;
	}

	protected function _applyOptionsPrice($product, $qty, $finalPrice)
	{
		if ($amount = $product->getCustomOption('giftcard_price')) {
			$finalPrice = $amount->getValue();
		}

		return parent::_applyOptionsPrice($product, $qty, $finalPrice);
	}

	public function getTotalPrices($product, $which = null, $includeTax = null)
	{
		$taxHelper = Mage::helper('tax');

		$giftcardAmount = $this->analyzeAmount($product);

		if ($giftcardAmount['type'] == 'fixed') {
			$minimalPrice = $maximalPrice = $taxHelper->getPrice($product, $giftcardAmount['price'], $includeTax, null, null, null, null, null, false);
		} elseif ($giftcardAmount['type'] == 'dropdown') {
			$minimalPrice = $taxHelper->getPrice($product, min($giftcardAmount['price']), $includeTax, null, null, null, null, null, false);
			$maximalPrice = $taxHelper->getPrice($product, max($giftcardAmount['price']), $includeTax, null, null, null, null, null, false);
		} else {
			$from         = $giftcardAmount['amount']['from'] * $giftcardAmount['price'] / 100;
			$to           = $giftcardAmount['amount']['to'] * $giftcardAmount['price'] / 100;
			$minimalPrice = $taxHelper->getPrice($product, $from, $includeTax, null, null, null, null, null, false);
			$maximalPrice = $taxHelper->getPrice($product, $to, $includeTax, null, null, null, null, null, false);
		}

		$minimalPrice = $product->getStore()->roundPrice($minimalPrice);
		$maximalPrice = $product->getStore()->roundPrice($maximalPrice);

		if ('max' == $which) {
			return $maximalPrice;
		} elseif ('min' == $which) {
			return $minimalPrice;
		}

		return array($minimalPrice, $maximalPrice);
	}

	public function roundAmount($amount)
	{
		return Mage::app()->getStore()->roundPrice($amount);
	}
}
