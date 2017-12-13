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
class Magegiant_GiftCardCredit_Block_Adminhtml_Order_Create_Form extends Magegiant_GiftCard_Block_Adminhtml_Order_Create_Form
{
	public function getCreditBalance()
	{
		return Mage::helper('giftcardcredit')->getCustomerBalance();
	}

	public function getUseCredit()
	{
		return Mage::getSingleton('adminhtml/session_quote')->getUseCredit();
	}

	public function getUsedCreditAmount()
	{
		return Mage::getSingleton('adminhtml/session_quote')->getGiftcardCreditAmount();
	}
}
