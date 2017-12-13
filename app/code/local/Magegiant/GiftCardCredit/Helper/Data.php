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
 * GiftCardCredit Helper
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardCredit_Helper_Data extends Magegiant_GiftCard_Helper_Data
{
	protected $_account;

	public function allowRedeem($giftCard)
	{
		if (!$giftCard->getAllowRedeem()) {
			return false;
		}

		if (!$this->getConfig('redeem/credit/allow_redeem_condition')) {
			$condition = $giftCard->getConditions()->getConditions();
			$action    = $giftCard->getActions()->getConditions();

			if (!empty($condition) || !empty($action)) {
				return false;
			}
		}

		return $this->getConfig('redeem/credit/allow_redeem');
	}

	public function allowGiftcardCreditBox()
	{
		$account = $this->getCreditAccount();
		if (!$account->getId() || !$account->getBalance()) {
			return false;
		}

		return parent::allowGiftcardBox();
	}

	public function applyCreditForQuote($amount, $useCredit = true)
	{
		$this->getSession()->setUseCredit($useCredit)
			->setGiftcardCreditAmount($amount);

		$this->getSession()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
		$this->getSession()->getQuote()->collectTotals()
			->save();

		return $this;
	}

	public function getCustomerBalance()
	{
		return $this->getCreditAccount()->getBalance();
	}

	public function getCustomerBalanceFormated()
	{
		return Mage::helper('core')->currency($this->getCreditAccount()->getBalance());
	}

	public function getCreditAccount($customerId = null)
	{
		if (!$this->_account) {
			$account = Mage::getModel('giftcardcredit/account');

			if (is_null($customerId)) {
				if (Mage::app()->getStore()->isAdmin()) {
					$customerId = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getCustomerId();
				} else {
					$customerId = Mage::getSingleton('customer/session')->getCustomerId();
				}
			}

			$account->load($customerId, 'customer_id');

			$this->_account = $account;
		}

		return $this->_account;
	}
}