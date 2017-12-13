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

class Magegiant_GiftCardCredit_Block_Checkout_Cart_Form extends Mage_Checkout_Block_Cart_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('magegiant/giftcardcredit/checkout/cart/form.phtml');
	}

	public function getCreditUsed()
	{
		return Mage::getSingleton('checkout/session')->getGiftcardCreditAmount();
	}
}
