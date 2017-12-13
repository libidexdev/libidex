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
class Magegiant_GiftCard_Model_Total_Quote_Giftcardincl extends Mage_Sales_Model_Quote_Address_Total_Abstract
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
		if ($applyTaxAfterDiscount) {
			$this->_processHiddenTaxes($address);

			return $this;
		}

		Mage::dispatchEvent($this->_codeTotal . '_collect_total_after', array(
			'address'    => $address,
			'total'      => $this,
			'before_tax' => false
		));

		try {
			$this->_helper->collect($address);
		} catch (Exception $e) {
			Mage::logException($e);
		}

		Mage::dispatchEvent($this->_codeTotal . '_collect_total_after', array(
			'address'    => $address,
			'total'      => $this,
			'before_tax' => false
		));

		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$quote                 = $address->getQuote();
		$applyTaxAfterDiscount = (bool)Mage::getStoreConfig(
			Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId()
		);
		if ($applyTaxAfterDiscount) {
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

	protected function _processHiddenTaxes($address)
	{
		foreach ($address->getAllItems() as $item) {
			if ($item->getParentItemId())
				continue;
			if ($item->getHasChildren() && $item->isChildrenCalculated()) {
				foreach ($item->getChildren() as $child) {
					$this->_addHiddenTax($child, $address);
				}
			} else {
				$this->_addHiddenTax($item, $address);
			}
		}
		if ($address->getGiftcardShippingHiddenTaxAmount()) {
			$address->addTotalAmount('shipping_hidden_tax', $address->getGiftcardShippingHiddenTaxAmount());
			$address->addBaseTotalAmount('shipping_hidden_tax', $address->getBaseGiftcardShippingHiddenTaxAmount());
		}
	}

	protected function _addHiddenTax($item, $address)
	{
		$item->setHiddenTaxAmount($item->getHiddenTaxAmount() + $item->getGiftcardHiddenTaxAmount());
		$item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() + $item->getBaseGiftcardHiddenTaxAmount());

		$address->addTotalAmount('hidden_tax', $item->getGiftcardHiddenTaxAmount());
		$address->addBaseTotalAmount('hidden_tax', $item->getBaseGiftcardHiddenTaxAmount());
	}

}
