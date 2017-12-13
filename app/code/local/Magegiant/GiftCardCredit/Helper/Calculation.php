<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardcredit Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardCredit_Helper_Calculation extends Magegiant_GiftCard_Helper_Calculation
{
	protected $_creditItemTotals;
	protected $_useForAddressId;
	protected $_baseGiftcardCreditAmount;
	protected $_giftcardCreditAmount;

	protected $_baseCreditUseForAddress;
	protected $_creditUseForAddress;

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$quote = $address->getQuote();
		if (!$this->canCalculate($quote) ||
			($quote->isVirtual() && $address->getAddressType() == 'shipping') ||
			(!$quote->isVirtual() && $address->getAddressType() == 'billing')
		) {
			return $this;
		}

		$this->setAddress($address);
		$this->_resetGiftcardData($address);

		$items = $address->getAllItems();
		if (!count($items)) {
			return $this;
		}

		$this->initTotals($items);

		$applyForGiftProduct = $this->getConfig('redeem/credit/allow_buy_giftcard');
		foreach ($items as $item) {
			if ($item->getParentItemId()) {
				continue;
			}
			if (!$applyForGiftProduct && ($item->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE)) {
				continue;
			}

			if ($item->getHasChildren() && $item->isChildrenCalculated()) {
				foreach ($item->getChildren() as $child) {
					$this->process($child);
				}
			} else {
				$this->process($item);
			}
		}

		if ($address->getShippingAmount()) {
			$this->processShippingAmount($address);
		}

		$quote->setGiftcardCreditAmount($address->getGiftcardCreditAmount());
		$quote->setBaseGiftcardCreditAmount($address->getBaseGiftcardCreditAmount());

		$this->getSession()->setGiftcardCreditAmount(abs($address->getGiftcardCreditAmount()));

		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address, $code)
	{
		if ($amount = $address->getGiftcardCreditAmount()) {
			$address->addTotal(array(
				'code'  => $code,
				'title' => Mage::helper('giftcardcredit')->__('Use Credit'),
				'value' => $amount
			));
		}

		return $this;
	}

	public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
	{
		$quote   = $item->getQuote();
		$address = $this->getAddress();

		$this->_resetGiftcardData($item);

		$qty           = $item->getTotalQty();
		$itemPrice     = $this->getItemPrice($item);
		$baseItemPrice = $this->getItemBasePrice($item);

		if ($itemPrice < 0) {
			return $this;
		}

		$itemDiscountPrice     = $item->getDiscountAmount() + $item->getGiftcardAmount();
		$baseItemDiscountPrice = $item->getBaseDiscountAmount() + $item->getBaseGiftcardAmount();

		if (empty($this->_creditItemTotals)) {
			Mage::throwException(Mage::helper('giftcard')->__('Item totals are not set.'));
		}

		/**
		 * prevent applying whole cart discount for every shipping order, but only for first order
		 */
		if ($quote->getIsMultiShipping()) {
			if ($this->_useForAddressId && $this->_useForAddressId != $address->getId()) {
				return $this;
			} else {
				$this->_useForAddressId = $address->getId();
			}
		}

		$discountRate        = $baseItemPrice * $qty / $this->_creditItemTotals['base_items_price'];
		$maximumItemDiscount = $this->_baseGiftcardCreditAmount * $discountRate;
		$quoteAmount         = $quote->getStore()->convertPrice($maximumItemDiscount);

		$discountAmount     = $quote->getStore()->roundPrice($quoteAmount);
		$baseDiscountAmount = $quote->getStore()->roundPrice($maximumItemDiscount);

		$discountAmount     = min($itemPrice * $qty - $itemDiscountPrice, $discountAmount);
		$baseDiscountAmount = min($baseItemPrice * $qty - $baseItemDiscountPrice, $baseDiscountAmount);

		$this->_baseCreditUseForAddress -= $baseDiscountAmount;
		$this->_creditUseForAddress -= $discountAmount;

		$item->setGiftcardCreditAmount($discountAmount);
		$item->setBaseGiftcardCreditAmount($baseDiscountAmount);

		$address->addTotalAmount('giftcard_credit', -$discountAmount);
		$address->addBaseTotalAmount('giftcard_credit', -$baseDiscountAmount);

		return $this;
	}

	public function processShippingAmount(Mage_Sales_Model_Quote_Address $address)
	{
		if (!Mage::helper('giftcard')->getConfig('redeem/credit/shipping')) {
			return $this;
		}

		$shippingAmount = $address->getShippingAmountForDiscount();
		if ($shippingAmount !== null) {
			$baseShippingAmount = $address->getBaseShippingAmountForDiscount();
		} else {
			$shippingAmount     = $address->getShippingAmount();
			$baseShippingAmount = $address->getBaseShippingAmount();
		}

		$discountAmount     = min(
			$shippingAmount - $address->getShippingDiscountAmount() - $address->getGiftcardShippingAmount(),
			$this->_creditUseForAddress
		);
		$baseDiscountAmount = min(
			$baseShippingAmount - $address->getBaseShippingDiscountAmount() - $address->getBaseGiftcardShippingAmount(),
			$this->_baseCreditUseForAddress
		);

		$address->setGiftcardCreditShippingAmount($discountAmount);
		$address->setBaseGiftcardCreditShippingAmount($baseDiscountAmount);

		$address->addTotalAmount('giftcard_credit', -$discountAmount);
		$address->addBaseTotalAmount('giftcard_credit', -$baseDiscountAmount);

		return $this;
	}

	public function initTotals($items)
	{
		$address = $this->getAddress();
		$address->setGiftcardUsedForAddress(array());

		if (!$items) {
			return $this;
		}

		$applyForGiftProduct = Mage::helper('giftcardcredit')->getConfig('redeem/credit/allow_buy_giftcard');

		$cardTotalItemsPrice     = 0;
		$cardTotalBaseItemsPrice = 0;

		foreach ($items as $item) {
			if ($item->getParentItemId()) {
				continue;
			}

			if (!$applyForGiftProduct && ($item->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE)) {
				continue;
			}

			$cardTotalItemsPrice += $this->getItemPrice($item) * $item->getTotalQty();
			$cardTotalBaseItemsPrice += $this->getItemBasePrice($item) * $item->getTotalQty();
		}


		$this->_creditItemTotals = array(
			'items_price'      => $cardTotalItemsPrice,
			'base_items_price' => $cardTotalBaseItemsPrice,
		);;

		return $this;
	}

	public function canCalculate($quote)
	{
		if (!Mage::helper('giftcard')->getSession()->getUseCredit()) {
			return false;
		}

		$useCreditAmount = $this->getUseCreditAmount();
		$customerBalance = Mage::helper('giftcardcredit')->getCustomerBalance();
		if ($useCreditAmount && $customerBalance) {
			$store               = $quote->getStore();
			$rate                = $store->getCurrentCurrencyRate();
			$baseUseCreditAmount = $store->roundPrice($useCreditAmount / $rate);
			if ($baseUseCreditAmount > $customerBalance) {
				$this->_baseGiftcardCreditAmount = $customerBalance;
				$this->_giftcardCreditAmount     = $store->convertPrice($this->_baseGiftcardCreditAmount);
			} else {
				$this->_giftcardCreditAmount     = $useCreditAmount;
				$this->_baseGiftcardCreditAmount = $baseUseCreditAmount;
			}

			$this->_creditUseForAddress     = $this->_giftcardCreditAmount;
			$this->_baseCreditUseForAddress = $this->_baseGiftcardCreditAmount;

			return true;
		}

		Mage::helper('giftcard')->getSession()->unsGiftcardCreditAmount();

		return false;
	}

	public function getUseCreditAmount()
	{
		return Mage::helper('giftcard')->getSession()->getGiftcardCreditAmount();
	}

	protected function _resetGiftcardData($object)
	{
		if ($object instanceof Mage_Sales_Model_Quote_Address) {
			$object->setTotalAmount('giftcard_credit', 0);
			$object->setBaseTotalAmount('giftcard_credit', 0);
		}

		$object->addData(array(
			'giftcard_credit_amount'               => 0,
			'base_giftcard_credit_amount'          => 0,
			'giftcard_credit_shipping_amount'      => 0,
			'base_giftcard_credit_shipping_amount' => 0,
		));

		return $this;
	}

	public function canRefundToCredit()
	{
		return $this->getConfig('redeem/calculation/refund') == Magegiant_GiftCardCredit_Model_Giftcard::REFUND_TO_CREDIT;
	}
}