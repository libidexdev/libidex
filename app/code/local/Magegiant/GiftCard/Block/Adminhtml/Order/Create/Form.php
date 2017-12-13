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
class Magegiant_GiftCard_Block_Adminhtml_Order_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('sales_order_create_giftcard_form');
	}

	public function getHeaderText()
	{
		return Mage::helper('giftcard')->__('Gift Cards');
	}

	public function getHeaderCssClass()
	{
		return 'head-promo-quote';
	}

	public function getLoadBlockUrl()
	{
		return $this->getUrl('adminhtml/giftcard_order/loadBlock');
	}

	public function getGiftcardCode()
	{
		$codes = $this->_getSession()->getGiftCodes();
		if ($codes && is_array($codes) && sizeof($codes)) {
			return $codes;
		}

		return null;
	}

	public function getSingleGiftCards()
	{
		if (!Mage::helper('giftcard')->getConfig('redeem/display/multiple')) {
			$giftCode = $this->getGiftcardCode();
			if ($giftCode) {
				return array_shift($giftCode);;
			}
		}

		return null;
	}
}
