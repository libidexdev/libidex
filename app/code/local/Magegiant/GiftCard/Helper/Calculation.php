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
class Magegiant_GiftCard_Helper_Calculation extends Magegiant_GiftCard_Helper_Data
{
	protected $_cartFixedRuleUsedForAddress;
	protected $_giftCardForQuote;
	protected $_giftcardItemTotals;
	protected $_address;

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$quote = $address->getQuote();
		if (!$this->_canCalculate($address) ||
			!$this->isEnabled($quote->getStoreId()) ||
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

		$applyForGiftProduct = Mage::helper('giftcard')->getConfig('redeem/calculation/allow_buy_giftcard');
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

					Mage::dispatchEvent('giftcard_quote_address_discount_item', array('item' => $child));

					$this->_aggregateItemDiscount($child, $address);
				}
			} else {
				$this->process($item);

				Mage::dispatchEvent('giftcard_quote_address_discount_item', array('item' => $item));

				$this->_aggregateItemDiscount($item, $address);
			}
		}

		if ($address->getShippingAmount()) {
			$this->processShippingAmount($address);
			$address->addTotalAmount('giftcard', -$address->getGiftcardShippingAmount());
			$address->addBaseTotalAmount('giftcard', -$address->getBaseGiftcardShippingAmount());
		}

		$this->_encodeGiftCards($address);

		$quote->setGiftcardAmount($address->getGiftcardAmount());
		$quote->setBaseGiftcardAmount($address->getBaseGiftcardAmount());

		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address, $code)
	{
		if ($amount = $address->getGiftcardAmount()) {
			$giftCards = $address->getGiftCards();
			if (!is_array($giftCards)) {
				$giftCards = Mage::helper('core')->jsonDecode($giftCards);
			}

			$giftCardsCollection = Mage::getModel('giftcard/giftcard')->getCollection()
				->addFieldToFilter('giftcard_id', array('in' => array_keys($giftCards)));
			foreach ($giftCardsCollection as $giftcard) {
				$giftCards[$giftcard->getId()] = array(
					'code'  => $giftcard->getCode(),
					'value' => $giftCards[$giftcard->getId()]
				);
			}

			$address->addTotal(array(
				'code'       => $code,
				'title'      => Mage::helper('giftcard')->__('Gift Card'),
				'value'      => $amount,
				'gift_cards' => $giftCards,
			));
		}

		return $this;
	}

	protected function _aggregateItemDiscount($item, $address)
	{
		$address->addTotalAmount('giftcard', -$item->getGiftcardAmount());
		$address->addBaseTotalAmount('giftcard', -$item->getBaseGiftcardAmount());

		$this->_addGiftcardListToObject($address, $item->getGiftCards());

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

		foreach ($this->getGiftCardsForQuote($address) as $card) {
			if (!$card->getActions()->validate($item)) {
				continue;
			}

			$itemDiscountPrice     = $item->getDiscountAmount() + $item->getGiftcardAmount();
			$baseItemDiscountPrice = $item->getBaseDiscountAmount() + $item->getBaseGiftcardAmount();

			$discountAmount     = 0;
			$baseDiscountAmount = 0;

			if (empty($this->_giftcardItemTotals[$card->getId()])) {
				Mage::throwException(Mage::helper('giftcard')->__('Item totals are not set.'));
			}

			/**
			 * prevent applying whole cart discount for every shipping order, but only for first order
			 */
			if ($quote->getIsMultiShipping()) {
				$usedForAddressId = $this->getCartFixedRuleUsedForAddress($card->getId());
				if ($usedForAddressId && $usedForAddressId != $address->getId()) {
					continue;
				} else {
					$this->setCartFixedRuleUsedForAddress($card->getId(), $address->getId());
				}
			}
			$cartRules = $address->getGiftcardUsedForAddress();
			if (!isset($cartRules[$card->getId()])) {
				$cartRules[$card->getId()] = $card->getAmount();
			}

			if ($cartRules[$card->getId()] > 0) {
				if ($this->_giftcardItemTotals[$card->getId()]['items_count'] <= 1) {
					$quoteAmount        = $quote->getStore()->convertPrice($cartRules[$card->getId()]);
					$baseDiscountAmount = min($baseItemPrice * $qty - $baseItemDiscountPrice, $cartRules[$card->getId()]);
				} else {
					$discountRate        = $baseItemPrice * $qty / $this->_giftcardItemTotals[$card->getId()]['base_items_price'];
					$maximumItemDiscount = $card->getAmount() * $discountRate;
					$quoteAmount         = $quote->getStore()->convertPrice($maximumItemDiscount);

					$baseDiscountAmount = min($baseItemPrice * $qty - $baseItemDiscountPrice, $maximumItemDiscount);
					$this->_giftcardItemTotals[$card->getId()]['items_count']--;
				}

				$discountAmount     = min($itemPrice * $qty - $itemDiscountPrice, $quoteAmount);
				$discountAmount     = $quote->getStore()->roundPrice($discountAmount);
				$baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);

				$cartRules[$card->getId()] -= $baseDiscountAmount;
			}
			$address->setGiftcardUsedForAddress($cartRules);

			$result = new Varien_Object(array(
				'discount_amount'      => $discountAmount,
				'base_discount_amount' => $baseDiscountAmount,
			));

			Mage::dispatchEvent('giftcard_validator_process', array(
				'gift_card' => $card,
				'item'      => $item,
				'address'   => $address,
				'quote'     => $quote,
				'qty'       => $qty,
				'result'    => $result,
			));

			$discountAmount     = $quote->getStore()->roundPrice($result->getDiscountAmount());
			$baseDiscountAmount = $quote->getStore()->roundPrice($result->getBaseDiscountAmount());

			$this->_addGiftcardListToObject($item, array($card->getId() => $baseDiscountAmount));

			$itemDiscountAmount     = $item->getGiftcardAmount();
			$itemBaseDiscountAmount = $item->getBaseGiftcardAmount();

			$discountAmount     = min($itemDiscountAmount + $discountAmount, $itemPrice * $qty);
			$baseDiscountAmount = min($itemBaseDiscountAmount + $baseDiscountAmount, $baseItemPrice * $qty);

			$item->setGiftcardAmount($discountAmount);
			$item->setBaseGiftcardAmount($baseDiscountAmount);
		}

		return $this;
	}

	public function processShippingAmount(Mage_Sales_Model_Quote_Address $address)
	{
		if (!$this->getConfig('redeem/calculation/shipping')) {
			return $this;
		}

		$shippingAmount = $address->getShippingAmountForDiscount();
		if ($shippingAmount !== null) {
			$baseShippingAmount = $address->getBaseShippingAmountForDiscount();
		} else {
			$shippingAmount     = $address->getShippingAmount();
			$baseShippingAmount = $address->getBaseShippingAmount();
		}
		$quote = $address->getQuote();
		foreach ($this->getGiftCardsForQuote($address) as $card) {
			$discountAmount     = 0;
			$baseDiscountAmount = 0;

			$cartRules = $address->getGiftcardUsedForAddress();
			if (!isset($cartRules[$card->getId()])) {
				$cartRules[$card->getId()] = $card->getAmount();
			}
			if ($cartRules[$card->getId()] > 0) {
				$quoteAmount        = $quote->getStore()->convertPrice($cartRules[$card->getId()]);
				$discountAmount     = min(
					$shippingAmount - $address->getShippingDiscountAmount() - $address->getGiftcardShippingAmount(),
					$quoteAmount
				);
				$baseDiscountAmount = min(
					$baseShippingAmount - $address->getBaseShippingDiscountAmount() - $address->getBaseGiftcardShippingAmount(),
					$cartRules[$card->getId()]
				);
				$cartRules[$card->getId()] -= $baseDiscountAmount;
			}

			$address->setGiftcardUsedForAddress($cartRules);

			$address->setGiftcardShippingAmount($address->getGiftcardShippingAmount() + $discountAmount);
			$address->setBaseGiftcardShippingAmount($address->getBaseGiftcardShippingAmount() + $baseDiscountAmount);

			$this->_addGiftcardListToObject($address, array($card->getId() => $baseDiscountAmount));
		}

		return $this;
	}

	public function initTotals($items)
	{
		$address = $this->getAddress();
		$address->setGiftcardUsedForAddress(array());

		if (!$items) {
			return $this;
		}

		$cardsItemTotals     = array();
		$applyForGiftProduct = Mage::helper('giftcard')->getConfig('redeem/calculation/allow_buy_giftcard');

		foreach ($this->getGiftCardsForQuote($address) as $card) {
			$cardTotalItemsPrice     = 0;
			$cardTotalBaseItemsPrice = 0;
			$validItemsCount         = 0;

			foreach ($items as $item) {
				if ($item->getParentItemId()) {
					continue;
				}

				if (!$applyForGiftProduct && ($item->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE)) {
					continue;
				}

				if (!$card->getActions()->validate($item)) {
					if ($message = $card->getConditionsDescription()) {
						$address->getQuote()->setHideGiftcardError(true);
						$this->getSession()->addError($this->__($message));
					}

					continue;
				}
				$cardTotalItemsPrice += $this->getItemPrice($item) * $item->getTotalQty();
				$cardTotalBaseItemsPrice += $this->getItemBasePrice($item) * $item->getTotalQty();
				$validItemsCount++;
			}

			$cardsItemTotals[$card->getId()] = array(
				'items_price'      => $cardTotalItemsPrice,
				'base_items_price' => $cardTotalBaseItemsPrice,
				'items_count'      => $validItemsCount,
			);
		}

		$this->_giftcardItemTotals = $cardsItemTotals;

		return $this;
	}

	public function getGiftCardsForQuote($address)
	{
		$quote = $address->getQuote();

		if (!$this->_giftCardForQuote) {
			$giftCardsCollection = Mage::getModel('giftcard/giftcard')->getCollection()
				->addFieldToFilter('code', array('in' => $quote->getGiftCodes()));

			$giftCodes = array();
			$giftCards = array();
			foreach ($giftCardsCollection as $card) {
				if (!$card->isActive(true, $quote->getWebsiteId(), true, true)) {
					continue;
				}

				$card->afterLoad();
				if ($card->validate($address)) {
					$giftCodes[] = $card->getCode();
					$giftCards[] = $card;
				} else {
					if ($message = $card->getConditionsDescription()) {
						$quote->setHideGiftcardError(true);
						$this->getSession()->addError($this->__($message));
					}
				}
			}

			$quote->setGiftCodes($giftCodes);
			$this->_giftCardForQuote = $giftCards;
		}

		return $this->_giftCardForQuote;
	}

	protected function _encodeGiftCards($address)
	{
		$giftCards = $address->getGiftCards();
		if (!is_array($giftCards) || !sizeof($giftCards)) {
			$this->getSession()->setGiftCodes(array());

			return $this;
		}

		$cardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('giftcard_id', array('in' => array_keys($giftCards)))
			->getColumnValues('code');
		$sessionCards   = $address->getQuote()->getGiftCodes();
		foreach ($sessionCards as $key => $card) {
			if (!in_array($card, $cardCollection)) {
				unset($sessionCards[$key]);
			}
		}
		$this->getSession()->setGiftCodes($sessionCards);

		/**
		 * Encode gift cards
		 */
		$address->setGiftCards(Mage::helper('core')->jsonEncode($giftCards));

		foreach ($address->getAllItems() as $item) {
			if ($item->getParentItemId())
				continue;
			if ($item->getHasChildren() && $item->isChildrenCalculated()) {
				foreach ($item->getChildren() as $child) {
					$giftCards = $child->getGiftCards();
					if (!is_array($giftCards) || !sizeof($giftCards)) {
						continue;
					}
					$child->setGiftCards(Mage::helper('core')->jsonEncode($giftCards));
				}
			} elseif ($item->getProduct()) {
				$giftCards = $item->getGiftCards();
				if (!is_array($giftCards) || !sizeof($giftCards)) {
					continue;
				}
				$item->setGiftCards(Mage::helper('core')->jsonEncode($giftCards));
			}
		}

		return $this;
	}

	protected function _resetGiftcardData($object)
	{
		if ($object instanceof Mage_Sales_Model_Quote_Address) {
			$object->setTotalAmount('giftcard', 0);
			$object->setBaseTotalAmount('giftcard', 0);
		}

		$object->addData(array(
			'giftcard_amount'               => 0,
			'base_giftcard_amount'          => 0,
			'giftcard_shipping_amount'      => 0,
			'base_giftcard_shipping_amount' => 0,
			'gift_cards'                    => ''
		));

		return $this;
	}

	protected function _addGiftcardListToObject($object, $cards)
	{
		if (!is_array($cards)) {
			return $this;
		}

		$giftCards = $object->getGiftCards();
		if (!is_array($giftCards)) {
			$giftCards = array();
		}
		foreach ($cards as $cardId => $discount) {
			if (!isset($giftCards[$cardId])) {
				$giftCards[$cardId] = 0;
			}
			$giftCards[$cardId] += $discount;
		}

		$object->setGiftCards($giftCards);

		return $this;
	}

	public function getItemPrice($item)
	{
		$price     = $item->getDiscountCalculationPrice();
		$calcPrice = $item->getCalculationPrice();

		return ($price !== null) ? $price : $calcPrice;
	}

	public function getItemBasePrice($item)
	{
		$price = $item->getDiscountCalculationPrice();

		return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
	}

	public function setAddress($address)
	{
		$this->_address = $address;

		return $this;
	}

	public function getAddress()
	{
		return $this->_address;
	}

	protected function _canCalculate($address)
	{
		$codes = $this->getSession()->getGiftCodes();
		if (!is_array($codes) || !sizeof($codes)) {
			return false;
		}

		if (!Mage::helper('giftcard')->getConfig('redeem/display/multiple')) {
			$codes = array(array_shift($codes));
		}

		$quote = $address->getQuote();

		if (!Mage::helper('giftcard')->getConfig('redeem/calculation/use_with_coupon') && $quote->getCouponCode()) {
			$this->getSession()->addError(Mage::helper('giftcard')->__('Gift Card code cannot be used with Coupon code.'));
			$this->getSession()->setGiftCodes(array());

			return false;
		}

		$quote->setGiftCodes($codes);

		return true;
	}

	public function setCartFixedRuleUsedForAddress($cardId, $itemId)
	{
		$this->_cartFixedRuleUsedForAddress[$cardId] = $itemId;
	}

	public function getCartFixedRuleUsedForAddress($cardId)
	{
		if (isset($this->_cartFixedRuleUsedForAddress[$cardId])) {
			return $this->_cartFixedRuleUsedForAddress[$cardId];
		}

		return null;
	}

	/**
	 * Refund Giftcard
	 */
	public function canRefundBackToGiftcard()
	{
		return $this->getConfig('redeem/calculation/refund') == Magegiant_GiftCard_Model_Source_Order_Refund::REFUND_TO_CARD;
	}
}