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
class Magegiant_GiftCard_Model_Total_Quote_Giftcardexcl extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_helper;
	protected $_codeTotal;

	public function __construct()
	{
		$this->_helper    = Mage::helper('giftcard/calculation');
		$this->_codeTotal = 'giftcard';
	}

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$quote                 = $address->getQuote();
		$applyTaxAfterDiscount = (bool)Mage::getStoreConfig(
			Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId()
		);
		if (!$applyTaxAfterDiscount) {
			return $this;
		}

		Mage::dispatchEvent($this->_codeTotal . '_collect_total_before', array(
			'address'    => $address,
			'total'      => $this,
			'before_tax' => true
		));

		try {
			$this->_helper->collect($address);

			$this->_prepareDiscountForItem($address);
		} catch (Exception $e) {
			Mage::logException($e);
		}

		Mage::dispatchEvent($this->_codeTotal . '_collect_total_after', array(
			'address'    => $address,
			'total'      => $this,
			'before_tax' => true
		));

		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$quote                 = $address->getQuote();
		$applyTaxAfterDiscount = (bool)Mage::getStoreConfig(
			Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId()
		);
		if (!$applyTaxAfterDiscount) {
			return $this;
		}
		try {
			$this->_helper->fetch($address, $this->getCode());
		} catch (Exception $e) {
			Mage::logException($e);
		}

		Mage::dispatchEvent($this->_codeTotal . '_fetch_total', array(
			'address' => $address,
			'total'   => $this
		));

		return $this;
	}

	public function _prepareDiscountForItem(Mage_Sales_Model_Quote_Address $address)
	{
		$items = $address->getAllItems();
		if (!count($items))
			return $this;
		$store = Mage::app()->getStore();

		foreach ($items as $item) {
			if ($item->getParentItemId())
				continue;
			if ($item->getHasChildren() && $item->isChildrenCalculated()) {
				foreach ($item->getChildren() as $child) {
					$this->_aggregateItemTax($child, $address);
				}
			} elseif ($item->getProduct()) {
				$this->_aggregateItemTax($item, $address);
			}
		}
		$baseDiscountForShipping = $address->getData('base_' . $this->_codeTotal . '_shipping_amount');
		if ($baseDiscountForShipping > 0) {
			$baseTaxableAmount = $address->getBaseShippingTaxable();
			$taxableAmount     = $address->getShippingTaxable();
			$address->setBaseShippingTaxable(max(0, $baseTaxableAmount - $baseDiscountForShipping));
			$address->setShippingTaxable(max(0, $taxableAmount - $address->getData($this->_codeTotal . '_shipping_amount')));

			if (Mage::helper('tax')->shippingPriceIncludesTax()) {
				$rate = $this->getShipingTaxRate($address, $store);
				if ($rate > 0) {
					$address->setBaseGiftcardShippingHiddenTaxAmount($this->calTax($baseTaxableAmount, $rate) - $this->calTax($address->getBaseShippingTaxable(), $rate));
					$address->setGiftcardShippingHiddenTaxAmount($this->calTax($taxableAmount, $rate) - $this->calTax($address->getShippingTaxable(), $rate));
				}
			}
		}

		return $this;
	}

	protected function _aggregateItemTax($item, $address)
	{
		$store             = Mage::app()->getStore();
		$baseTaxableAmount = $item->getBaseTaxableAmount();
		$taxableAmount     = $item->getTaxableAmount();
		$rateQty           = $this->getRateQty($item, $store);

		$item->setBaseTaxableAmount(max(0, $baseTaxableAmount - $item->getData('base_' . $this->_codeTotal . '_amount') / $rateQty));
		$item->setTaxableAmount(max(0, $taxableAmount - $item->getData($this->_codeTotal . '_amount') / $rateQty));

		if (Mage::helper('tax')->priceIncludesTax()) {
			$rate = $this->getItemRateOnQuote($address, $item->getProduct(), $store);
			if ($rate > 0) {
				$item->setBaseGiftcardHiddenTaxAmount($rateQty * ($this->calTax($baseTaxableAmount, $rate) - $this->calTax($item->getBaseTaxableAmount(), $rate)));
				$item->setGiftcardHiddenTaxAmount($rateQty * ($this->calTax($taxableAmount, $rate) - $this->calTax($item->getTaxableAmount(), $rate)));
			}
		}

		return $this;
	}

	public function getRateQty($item, $store)
	{
		if (Mage::getSingleton('tax/config')->getAlgorithm($store) == Mage_Tax_Model_Calculation::CALC_UNIT_BASE) {
			return $item->getTotalQty();
		}

		return 1;
	}

	public function getItemRateOnQuote($address, $product, $store)
	{
		$taxClassId = $product->getTaxClassId();
		if ($taxClassId) {
			$request = Mage::getSingleton('tax/calculation')->getRateRequest(
				$address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
			);
			$rate    = Mage::getSingleton('tax/calculation')
				->getRate($request->setProductClassId($taxClassId));

			return $rate;
		}

		return 0;
	}

	public function getShipingTaxRate($address, $store)
	{
		$request = Mage::getSingleton('tax/calculation')->getRateRequest(
			$address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
		);
		$request->setProductClassId(Mage::getSingleton('tax/config')->getShippingTaxClass($store));
		$rate = Mage::getSingleton('tax/calculation')->getRate($request);

		return $rate;
	}

	public function calTax($price, $rate)
	{
		return $this->round(Mage::getSingleton('tax/calculation')->calcTaxAmount($price, $rate, true, false));
	}

	public function round($price)
	{
		return Mage::getSingleton('tax/calculation')->round($price);
	}

}
