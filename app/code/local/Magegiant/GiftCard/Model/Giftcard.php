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
class Magegiant_GiftCard_Model_Giftcard extends Mage_Rule_Model_Abstract
{
	const XML_PATH = 'giftcard/general/';
	const PRODUCT_TYPE = 'giantcard';

	const TYPE_VIRTUAL = 0;
	const TYPE_PHYSICAL = 1;
	const TYPE_COMBINED = 2;

	const STATUS_AVAILABLE = 1;
	const STATUS_EXPIRED = 2;
	const STATUS_USED = 3;
	const STATUS_REFUNDED = 4;

	const STATE_INACTIVE = 0;
	const STATE_ACTIVE = 1;

	protected $_eventPrefix = 'magegiant_giftcard';
	protected $_eventObject = 'giftcard';

	public function _construct()
	{
		$this->_init('giftcard/giftcard');
	}

	public function subtract($amount)
	{
		if ($this->isActive($amount, false, false, false)) {
			$this->setAmountChange(-$amount)
				->setAmount($this->getAmount() - $amount)
				->setAction(Magegiant_GiftCard_Model_History::ACTION_USED);
		}

		return $this;
	}

	public function add($amount)
	{
		if ($amount > 0 && $this->isActive(false, false, true, true)) {
			$this->setAmountChange($amount)
				->setAmount($this->getAmount() + $amount)
				->setAction(Magegiant_GiftCard_Model_History::ACTION_UPDATED);
		}

		return $this;
	}

	protected function _beforeSave()
	{
		if ($this->getAmount() < 0) {
			throw new Mage_Core_Exception(
				Mage::helper('giftcard')->__('Balance cannot be less than zero.')
			);
		}

		if ($this->isObjectNew()) {
			$this->setCreatedAt(Varien_Date::now(true));

			if (!$this->hasCode()) {
				$this->generateCode($this->getPattern());
			}

			if (!$this->hasAction()) {
				$this->setAction(Magegiant_GiftCard_Model_History::ACTION_CREATED);
			}
		} elseif ($this->getOrigData('amount') != $this->getAmount()) {
			$this->setAmountChange($this->getAmount() - $this->getOrigData('amount'));
			if (!$this->hasAction()) {
				$this->setAction(Magegiant_GiftCard_Model_History::ACTION_UPDATED);
			}
		}

		$this->_calExpiredAt()
			->_calStatus();

		return parent::_beforeSave();
	}

	protected function _afterSave()
	{
		parent::_afterSave();

		if ($this->getAction()) {
			Mage::getModel('giftcard/history')->setGiftCard($this)->saveData();
		}

		if ($this->isActive(true, false, true, true)
			&& !$this->getEmailSent()
			&& $this->getRecipientEmail()
			&& !$this->isSchedule()
			&& $this->getData('allow_send_email')
		) {
			$this->unsetData('history_detail');
			$this->sendEmail();

			if (Mage::helper('giftcard')->getConfig('email/purchase_email')) {
				$this->sendPurchaserEmail();
			}
		}

		return $this;
	}

	public function isSchedule()
	{
		if (!$this->getScheduleAt()) {
			return false;
		}

		$currentDate = strtotime(Mage::getModel('core/date')->date('Y-m-d'));

		if (strtotime($this->getScheduleAt()) > $currentDate) {
			return true;
		}

		return false;
	}

	public function isExpired()
	{
		if (!$this->getExpiredAt()) {
			return false;
		}

		$currentDate = strtotime(Mage::getModel('core/date')->date('Y-m-d'));

		if (strtotime($this->getExpiredAt()) < $currentDate) {
			return true;
		}

		return false;
	}

	protected function _calExpiredAt()
	{
		if (is_numeric($this->getExpire()) && $this->getExpire() > 0) {
			$this->setExpiredAt(date('Y-m-d', strtotime("now +{$this->getExpire()}days")));
		} else {
			if ($this->getExpiredAt()) {
				if ($this->isObjectNew()) {
					$expirationDate = Mage::app()->getLocale()->date($this->getExpiredAt(), Varien_Date::DATE_INTERNAL_FORMAT, null, false);
					$currentDate    = Mage::app()->getLocale()->date(null, Varien_Date::DATE_INTERNAL_FORMAT, null, false);
					if ($expirationDate < $currentDate) {
						throw new Mage_Core_Exception(
							Mage::helper('giftcard')->__('Expiration date cannot be in the past.')
						);
					}
				} else {
					if ($this->isExpired()) {
						$this->setStatus(self::STATUS_EXPIRED);
					} else {
						$this->setStatus(self::STATUS_AVAILABLE);
					}
				}
			} else {
				$this->setExpiredAt(null);
			}
		}

		return $this;
	}

	protected function _calStatus()
	{
		if ($this->isObjectNew() || $this->getStatus() == self::STATUS_REFUNDED) {
			return $this;
		}

		if ($this->hasStatusChange()) {
			$this->setStatus($this->getStatusChange());

			return $this;
		}

		if (($this->getOrigData('amount') != $this->getAmount())) {
			$this->setStatus(($this->getAmount() > 0) ? self::STATUS_AVAILABLE : self::STATUS_USED);
		}

		if ($this->getAmount() > 0) {
			$this->setStatus($this->isExpired() ? self::STATUS_EXPIRED : self::STATUS_AVAILABLE);
		}

		return $this;
	}

	public function generateCode($pattern = null)
	{
		if (is_null($pattern)) {
			$pattern = $this->getPattern();
		}

		$attempt = 10;
		do {
			if ($attempt <= 0) {
				Mage::throwException(
					Mage::helper('giftcard')->__('Unable to generate code. Please check the setting and try again.')
				);
			}
			$code = Mage::helper('giftcard')->generateCode($pattern);
			$attempt--;
		} while ($this->getResource()->checkCode($code));

		$this->setCode($code);

		return $code;
	}

	public function isActive($checkAmount = true, $checkWebsite = false, $checkStatus = false, $checkExpire = true)
	{
		if (!$this->getId()) {
			return false;
		}

		if ($checkWebsite) {
			if (!is_numeric($checkWebsite)) {
				$checkWebsite = Mage::app()->getWebsite()->getId();
			}
			if ($this->getWebsiteId() != $checkWebsite) {
				return false;
			}
		}

		if ($checkStatus && (!$this->getActive() || $this->getStatus() != self::STATUS_AVAILABLE)) {
			return false;
		}

		if ($checkExpire && $this->isExpired()) {
			return false;
		}

		if ($checkAmount) {
			if ($this->getAmount() <= 0) {
				return false;
			}
			if (is_numeric($checkAmount) && $this->getAmount() < $checkAmount) {
				return false;
			}
		}

		return true;
	}

	public function createMultiple($data = null)
	{
		if (!$data) {
			$this->_beforeSave();
			$codes = $this->getResource()->createMultiple($this);
		} else {
			$codes = $this->getResource()->createMultiple($data);
		}

		$giftCards = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('code', array('in' => $codes));
		if ($giftCards->getSize()) {
			Mage::getResourceModel('giftcard/history')->createMultiple($giftCards, $this->getHistoryDetail());
		}

		return $giftCards;
	}

	protected function _afterLoad()
	{
		$this->setConditions(null);
		$this->setActions(null);

		$this->_setStatusLabel();

		return parent::_afterLoad();
	}

	public function getBalanceFormated()
	{
		return Mage::app()->getStore()->convertPrice($this->getAmount(), true);
	}

	public function expire($collection = null)
	{
		if (is_null($collection)) {
			$this->setStatusChange(self::STATUS_EXPIRED)
				->save();
		} else {
			$this->updateStatus($collection, self::STATUS_EXPIRED);
		}

		return $this;
	}

	public function refund($collection = null)
	{
		if (is_null($collection)) {
			$this->setStatusChange(self::STATUS_REFUNDED)
				->save();
		} else {
			$this->updateStatus($collection, self::STATUS_REFUNDED);
		}

		return $this;
	}

	public function active()
	{
		$this->setActive(self::STATE_ACTIVE)
			->save();

		return $this;
	}

	public function updateStatus($collection, $status)
	{
		$this->getResource()->updateStatus($collection->getAllIds(), $status);

		if ($this->getHistoryDetail()) {
			Mage::getResourceModel('giftcard/history')->updateStatus($collection, $status, $this->getHistoryDetail());
		}

		return $this;
	}

	public function loadByItemId($itemId, $status = array(self::STATUS_AVAILABLE), $state = null)
	{
		$collection = $this->getCollection()
			->addFieldToFilter('item_id', $itemId);

		if (is_array($status)) {
			$collection->addFieldToFilter('status', array('in' => $status));
		}

		if (!is_null($state)) {
			$collection->addFieldToFilter('active', $state);
		}

		return $collection;
	}

	public function sendEmail()
	{
		$template = Mage::helper('giftcard')->getConfig('email/template', $this->getStore());

		$email = $this->_send($template, 'recipient');

		$this->_afterLoad();
		$this->setEmailSent(false);
		if ($email->getSentSuccess()) {
			$this->setEmailSent(true)
				->setAction(Magegiant_GiftCard_Model_History::ACTION_SENT)
				->save();
		}

		return $this;
	}

	public function sendReminderEmail()
	{
		$template = Mage::helper('giftcard')->getConfig('email/reminder_email_template', $this->getStore());

		$sentTo = Mage::helper('giftcard')->getConfig('email/reminder_receive');
		if ($sentTo == 'both') {
			$send1 = $this->_send($template, 'sender');
			$send2 = $this->_send($template, 'recipient');

			$check     = $send1->getSentSuccess() && $send2->getSentSuccess();
			$recipient = $this->getSenderNameWithEmail() . ', ' . $this->getRecipientNameWithEmail();
		} else {
			$send = $this->_send($template, $sentTo);

			$check     = $send->getSentSuccess();
			$recipient = ($sentTo == 'sender') ? $this->getSenderNameWithEmail() : $this->getRecipientNameWithEmail();
		}

		if ($check) {
			$this->_afterLoad()
				->setReminderSent(true)
				->setAction(Magegiant_GiftCard_Model_History::ACTION_SENT)
				->setHistoryDetail(Mage::helper('giftcard')->__('Reminder Email Sent. Recipient: %s', $recipient))
				->save();
		}

		return $this;
	}

	public function sendPurchaserEmail()
	{
		$template = Mage::helper('giftcard')->getConfig('email/purchase_email_template', $this->getStore());

		$this->_send($template, 'sender');

		return $this;
	}

	protected function _send($template, $sendTo)
	{
		$customerName  = $this->getData($sendTo . '_name');
		$customerEmail = $this->getData($sendTo . '_email');
		if (!$customerEmail) {
			return $this;
		}
		if (!$customerName) {
			$customerName = $customerEmail;
		}

		$email = Mage::getModel('core/email_template')->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStore()));
		$email->sendTransactional(
			$template,
			Mage::getStoreConfig('giftcard/email/identity', $this->getStore()),
			$customerEmail,
			$customerName,
			array(
				'giftcard' => $this,
				'store_phone'     => Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE, $this->getStore()),
				'store_email'     => Mage::getStoreConfig(Mage_Customer_Helper_Data::XML_PATH_SUPPORT_EMAIL, $this->getStore())
			)
		);

		return $email;
	}

	public function getStore()
	{
		if (!$this->hasData('store')) {
			if (is_null($this->getData('store_id'))) {
				$store = Mage::app()->getWebsite($this->getWebsiteId())->getDefaultStore();
			} else {
				$store = Mage::app()->getStore($this->getData('store_id'));
			}

			$this->setData('store', $store);
		}

		return $this->getData('store');
	}

	public function getSenderNameWithEmail()
	{
		$name = $this->getSenderName();

		if ($email = $this->getSenderEmail()) {
			$name .= ' &lt;' . $email . '&gt;';
		}

		return $name;
	}

	public function getRecipientNameWithEmail()
	{
		$name = $this->getRecipientName();

		if ($email = $this->getRecipientEmail()) {
			$name .= ' &lt;' . $email . '&gt;';
		}

		return $name;
	}

	protected function _setStatusLabel()
	{
		$this->setStatusLabel($this->getStatusLabel());
	}

	public function getStatusLabel($status = null)
	{
		if (is_null($status)) {
			$status = $this->getStatus();
		}

		$statuses = $this->getStatusArray();
		if (isset($statuses[$status])) {
			$statusLabel = $statuses[$status];

			return $statusLabel;
		}

		return '';
	}

	public function getStatusArray()
	{
		$statuses = new Varien_Object(array(
			self::STATUS_AVAILABLE => Mage::helper('giftcard')->__('Available'),
			self::STATUS_EXPIRED   => Mage::helper('giftcard')->__('Expired'),
			self::STATUS_USED      => Mage::helper('giftcard')->__('Used'),
			self::STATUS_REFUNDED  => Mage::helper('giftcard')->__('Refunded'),
		));

		Mage::dispatchEvent('magegiant_giftcard_get_status_array', array(
			'statuses'          => $statuses,
			$this->_eventObject => $this
		));

		return $statuses->getData();
	}

	public function getStateLabel($state = null)
	{
		if (is_null($state)) {
			$state = $this->getActive();
		}

		$states = $this->getStateArray();
		if (isset($states[$state])) {
			$stateLabel = $states[$state];

			return $stateLabel;
		}

		return '';
	}

	public function getStateArray()
	{
		return array(
			self::STATE_ACTIVE   => Mage::helper('giftcard')->__('Active'),
			self::STATE_INACTIVE => Mage::helper('giftcard')->__('Inactive'),
		);
	}

	public function getConditionsInstance()
	{
		return Mage::getModel('salesrule/rule_condition_combine');
	}

	public function getActionsInstance()
	{
		return Mage::getModel('salesrule/rule_condition_product_combine');
	}
}