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
class Magegiant_GiftCard_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_ENABLED = 'giftcard/general/is_enable';
	const XML_PATH_GENERAL_CONFIG = 'giftcard/general/';

	public function isEnabled($storeId = null)
	{
		if (!$storeId) {
			$storeId = Mage::app()->getStore()->getId();
		}

		return Mage::getStoreConfig(self::XML_PATH_ENABLED, $storeId);
	}

	public function getConfig($name, $storeId = null)
	{
		if (!$storeId) {
			$storeId = Mage::app()->getStore()->getId();
		}

		return Mage::getStoreConfig('giftcard/' . $name, $storeId);
	}

	public function generateCode($pattern)
	{
		$patternString = '#\{([0-9]+)([LD]{1,2})\}#';
		if (preg_match($patternString, $pattern)) {
			return preg_replace_callback($patternString, array($this, 'generatePattern'), $pattern);
		} else {
			return $pattern;
		}
	}

	public function generatePattern($param)
	{
		$pool = (strpos($param[2], 'L')) === false ? '' : self::CHARS_UPPERS;
		$pool .= (strpos($param[2], 'D')) === false ? '' : self::CHARS_DIGITS;

		return $this->getRandomString($param[1], $pool);
	}

	public function addGiftCode($giftCode)
	{
		$giftCard = Mage::getModel('giftcard/giftcard')->load($giftCode, 'code');
		if (!$giftCard->getId() || !$giftCard->isActive(true, true, true, true)) {
			Mage::throwException($this->__('Wrong Gift Card Code.'));
		}

		$giftCodeList = $this->getSession()->getGiftCodes();
		if (!is_array($giftCodeList)) {
			$giftCodeList = array();
		}

		if (!in_array($giftCode, $giftCodeList)) {
			$giftCodeList[] = $giftCode;
		}

		$this->getSession()->setGiftCodes($giftCodeList);

		$quote = $this->getSession()->getQuote();
		if (!$this->getConfig('redeem/calculation/use_with_coupon') && $quote->getCouponCode()) {
			$this->getSession()->addNotice($this->__('Gift Card code cannot be used with Coupon code.'));
			$quote->setCouponCode('');
		}

		$this->getSession()->getQuote()->collectTotals()
			->save();

		if (!in_array($giftCode, $this->getSession()->getGiftCodes())) {
			if($this->getSession()->getQuote()->getHideGiftcardError()){
				return false;
			}

			Mage::throwException($this->__('Gift Card code "%s" cannot be used for this order.', Mage::helper('core')->escapeHtml($giftCode)));
		}

		return true;
	}

	public function getCodeDisplay($code)
	{
		if (!$this->getConfig('gift_code/hidden')) {
			return $code;
		}

		$numOfPrefix = (int)$this->getConfig('gift_code/numprefix');
		$numOfSuffix = (int)$this->getConfig('gift_code/numsuffix');
		$hiddenChar  = (string)$this->getConfig('gift_code/hiddenchar');

		$codeLength = strlen($code);

		$prefix = substr($code, 0, $numOfPrefix);
		$suffix = substr($code, -$numOfSuffix);
		$hidden = str_repeat($hiddenChar, $codeLength - $numOfPrefix - $numOfSuffix);

		return $prefix . $hidden . $suffix;
	}

	public function getSession()
	{
		if (Mage::app()->getStore()->isAdmin()) {
			return Mage::getSingleton('adminhtml/session_quote');
		}

		return Mage::getSingleton('checkout/session');
	}

	public function isGiftcardOrder($quote = null)
	{
		$address = ($quote->isVirtual()) ? $quote->getBillingAddress() : $quote->getShippingAddress();

		foreach ($address->getAllItems() as $item) {
			if ($item->getProductType() != Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
				return false;
			}
		}

		return true;
	}

	public function allowGiftcardBox($quote = null)
	{
		if (!$quote) {
			$quote = $this->getSession()->getQuote();
		}

		if (!$this->getConfig('redeem/calculation/allow_buy_giftcard') && $this->isGiftcardOrder($quote)) {
			return false;
		}

		return true;
	}

	public function getConfigData($product, $code)
	{
		if ($product->getData('use_config_' . $code)) {
			$group = Mage::helper('giftcard')->getConfig('map/' . $code);
			if (!$group) $group = 'general';

			return Mage::helper('giftcard')->getConfig($group . '/' . $code);
		} else {
			return $product->getData($code);
		}
	}

	public function getListOfGiftcard()
	{
		$session = Mage::getSingleton('customer/session');
		if (!$session->isLoggedIn()) {
			return null;
		}

		$customerId   = $session->getCustomerId();
		$giftCardList = Mage::getModel('giftcard/giftcard_list')->getCollection()
			->addFieldToFilter('customer_id', $customerId)
			->getColumnValues('giftcard_id');

		$giftCard = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('giftcard_id', array('in' => $giftCardList))
			->addFieldToFilter('amount', array('gt' => 0))
			->addFieldToFilter('status', Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE)
			->addFieldToFilter('active', Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE);

		return $giftCard;
	}
}