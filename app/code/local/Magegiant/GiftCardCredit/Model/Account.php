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
class Magegiant_GiftCardCredit_Model_Account extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('giftcardcredit/account');
	}

	public function redeem($giftCard)
	{
		if (!$giftCard instanceof Magegiant_GiftCard_Model_Giftcard) {
			$giftCard = Mage::getModel('giftcard/giftcard')->load((string)$giftCard, 'code');
		}

		if (!$giftCard->isActive(true, true, true, true)) {
			Mage::throwException($this->_helper()->__('Invalid Gift Code.'));
		}

		if (!Mage::helper('giftcardcredit')->allowRedeem($giftCard)) {
			Mage::throwException($this->_helper()->__('Gift Card cannot be redeemed.'));
		}

		$this->_prepareSave()
			->setBalance($this->getBalance() + $giftCard->getAmount());

		$giftCard->subtract($giftCard->getAmount())
			->setStatusChange(Magegiant_GiftCardCredit_Model_Giftcard::STATUS_REDEEM)
			->setAction(Magegiant_GiftCardCredit_Model_Giftcard::ACTION_REDEEMED)
			->setHistoryDetail($this->_helper()->__('Redeemed by %s.', $this->getCustomerNameWithEmail()));

		Mage::getModel('core/resource_transaction')
			->addObject($this)
			->addObject($giftCard)
			->save();

		return $this;
	}

	public function spend($amount)
	{
		if (!$this->getId() || !$this->getBalance()) {
			Mage::log(Mage::helper('giftcardcredit')->__('There is an error when using credit for the order.'));
		}

		$balance = max(0, $this->getBalance() - abs($amount));

		try {
			$this->setBalance($balance)
				->save();
		} catch (Exception $e) {
			Mage::logException($e);
		}

		return $this;
	}

	public function refund($amount){
		if (!$this->getId()) {
			Mage::log(Mage::helper('giftcardcredit')->__('There is an error when refund credit to account balance.'));
		}

		$balance = $this->getBalance() + abs($amount);

		try {
			$this->setBalance($balance)
				->save();
		} catch (Exception $e) {
			Mage::logException($e);
		}

		return $this;
	}

	protected function _prepareSave()
	{
		if ($this->isObjectNew()) {
			if (!$this->getCustomerId()) {
				$this->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
			}

			$customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
			if (!$customer->getId()) {
				Mage::throwException($this->_helper()->__('Invalid Account to redeem'));
			}

			$this->addData(array(
				'website_id' => $customer->getWebsiteId(),
				'created_at' => Varien_Date::now(true),
				'customer'   => $customer
			));
		}

		return $this;
	}

	public function getCustomerNameWithEmail()
	{
		if (!($customer = $this->getCustomer())) {
			$customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
		}

		if (!$customer->getId()) {
			return '';
		}

		return $customer->getName() . ' &lt;' . $customer->getEmail() . '&gt;';
	}

	protected function _helper()
	{
		return Mage::helper('giftcardcredit');
	}
}