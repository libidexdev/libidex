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
class Magegiant_GiftCard_Block_Checkout_Cart_Form extends Mage_Checkout_Block_Cart_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('magegiant/giftcard/checkout/cart/form.phtml');
	}

	public function allowCheck()
	{
		if (Mage::helper('giftcard')->getConfig('redeem/display/check_code')) {
			return true;
		}

		return false;
	}

	public function getGiftCard()
	{
//		if (!Mage::helper('giftcard')->getConfig('redeem/display/multiple')) {
//			$giftCode = $this->getCheckout()->getGiftCodes();
//			if ($giftCode && is_array($giftCode) && (sizeof($giftCode == 1))) {
//				$giftCode = array_shift($giftCode);
//
//				return $giftCode;
//			}
//		}

		return null;
	}
}
