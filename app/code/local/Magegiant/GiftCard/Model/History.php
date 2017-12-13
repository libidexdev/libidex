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

class Magegiant_GiftCard_Model_History extends Mage_Core_Model_Abstract
{
	const ACTION_CREATED = 1;
	const ACTION_UPDATED = 2;
	const ACTION_SENT = 3;
	const ACTION_USED = 4;
	const ACTION_EXPIRED = 5;

	protected $_eventPrefix = 'magegiant_giftcard_history';
	protected $_eventObject = 'history';

	public function _construct()
	{
		$this->_init('giftcard/history');
	}

	public function getActionArray()
	{
		$container = new Varien_Object(array(
			self::ACTION_CREATED  => Mage::helper('giftcard')->__('Created'),
			self::ACTION_UPDATED  => Mage::helper('giftcard')->__('Updated'),
			self::ACTION_SENT     => Mage::helper('giftcard')->__('Sent'),
			self::ACTION_USED     => Mage::helper('giftcard')->__('Used'),
			self::ACTION_EXPIRED  => Mage::helper('giftcard')->__('Expired'),
		));
		Mage::dispatchEvent('giftcard_history_get_action_array', array(
			'actions' => $container
		));

		return $container->getData();
	}

	public function getActionLabel()
	{
		$actions = $this->getActionArray();

		if (isset($actions[$this->getAction()])) {
			$actionLabel = $actions[$this->getAction()];

			return $actionLabel;
		}

		return '';
	}

	public function saveData()
	{
		$giftCard = $this->getGiftCard();
		if (!$giftCard || !$giftCard->getId()) {
			return $this;
		}

		$data = array(
			'action'         => $giftCard->getAction(),
			'giftcard_id'    => $giftCard->getId(),
			'giftcard_code'  => $giftCard->getCode(),
			'amount'         => $giftCard->getAmount(),
			'amount_change'  => $giftCard->getAmountChange(),
			'updated_at'     => Varien_Date::now(),
			'history_detail' => $giftCard->getHistoryDetail()
		);

		if (!$giftCard->getHistoryDetail()) {
			$message = '';
			switch ($data['action']) {
				case self::ACTION_CREATED:
					if ($giftCard->getOrder()) {
						$message = Mage::helper('giftcard')->__('Order #%s.', $giftCard->getOrder()->getIncrementId());
					} elseif (Mage::app()->getStore()->isAdmin()) {
						$admin = Mage::getSingleton('admin/session')->getUser()->getUsername();
						if ($admin) {
							$message = Mage::helper('giftcard')->__('Created by %s.', $admin);
						}
					}
					break;
				case self::ACTION_UPDATED:
					if (Mage::app()->getStore()->isAdmin()) {
						$admin = Mage::getSingleton('admin/session')->getUser()->getUsername();
						if ($admin) {
							$message = Mage::helper('giftcard')->__('Updated by %s.', $admin);
						}
					}
					break;
				case self::ACTION_USED:
					if ($giftCard->getOrder()) {
						$message = Mage::helper('giftcard')->__('Order #%s. Used by %s.', $giftCard->getOrder()->getIncrementId(), $giftCard->getOrder()->getCustomerEmail());
					}
					break;
				case self::ACTION_EXPIRED:
					break;
				case self::ACTION_SENT:
					if ($recipient = $giftCard->getRecipientEmail()) {
						if ($name = $giftCard->getRecipientName()) {
							$recipient = $name . ' <' . $recipient . '>';
						}

						$message = Mage::helper('giftcard')->__('Recipient: %s.', $recipient);

						if ($sender = $giftCard->getSenderEmail()) {
							if ($name = $giftCard->getSenderName()) {
								$sender = $name . ' <' . $sender . '>';
							}

							$message .= '&nbsp;' . Mage::helper('giftcard')->__('Sender: %s.', $sender);
						}

						if ($sendBy = $giftCard->getSendBy()) {
							$message .= '&nbsp;' . Mage::helper('giftcard')->__('Sent by %s.', $sendBy);
						}

					}
					break;
				default:
					return $this;
			}
			$data['history_detail'] = $message;
		}

		$this->setData($data)
			->save();

		return $this;
	}
}