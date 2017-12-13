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
 * @package     Magegiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardcredit Block
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardCredit
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardCredit_Block_Account_Giftcard_Credit extends Magegiant_GiftCard_Block_Abstract
{
	public function getCreditBalance()
	{
		$session = Mage::getSingleton('customer/session');

		if ($session->isLoggedIn()) {
			$account = Mage::getModel('giftcardcredit/account')->load($session->getCustomerId(), 'customer_id');
			if ($account->getId()) {
				return Mage::helper('core')->currency($account->getBalance());
			}
		}

		return 0;
	}
}