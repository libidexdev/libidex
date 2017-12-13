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
class Magegiant_GiftCard_Model_Observer
{
	protected $_calculators = array();

	public function productLoadAfter($observer)
	{
		$product = $observer->getEvent()->getProduct();

		if (!is_array($product->getData('giftcard_amount'))) {
			$amount = $product->getData('giftcard_amount');

			try {
				$amount = empty($amount) ? false : unserialize($amount);
			} catch (Exception $e) {

			}

			$product->setData('giftcard_amount', $amount);
		}

		return $this;
	}

	public function paypalPrepareLineItems($observer)
	{
		if ($paypalCart = $observer->getPaypalCart()) {
			$salesEntity = $paypalCart->getSalesEntity();

			$baseDiscount = abs($salesEntity->getBaseGiftcardAmount());
			if ($baseDiscount > 0.0001) {
				$paypalCart->updateTotal(
					Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, (float)$baseDiscount, Mage::helper('giftcard')->__('Gift Card')
				);
			}
		}

		return $this;
	}

	public function salesQuoteSubmitBefore($observer)
	{
		$session = Mage::helper('giftcard')->getSession();
		$session->unsGiftCodes();

		$order = $observer->getEvent()->getOrder();
		$quote = $observer->getEvent()->getQuote();

		try {
			$giftCards = Mage::helper('core')->jsonDecode($order->getGiftCards());
		} catch (Exception $e) {
			return $this;
		}

		if (is_array($giftCards)) {
			$applyCart = array();
			foreach ($giftCards as $cardId => $cardAmount) {
				try {
					Mage::getModel('giftcard/giftcard')
						->load($cardId)
						->subtract($cardAmount)
						->setOrder($order)
						->save();
					$applyCart[$cardId] = $cardAmount;
				} catch (Mage_Core_Exception $e) {
					$quote->setErrorMessage($e->getMessage());
				}
			}

			$order->setApplyCart($applyCart);
		}

		return $this;
	}

	public function salesQuoteSubmitFailure($observer)
	{
		$order = $observer->getEvent()->getOrder();

		if ($applyCart = $order->getApplyCart()) {
			foreach ($applyCart as $cardId => $cardAmount) {
				try {
					Mage::getModel('giftcard/giftcard')
						->load($cardId)
						->add($cardAmount)
						->setOrder($order)
						->save();
				} catch (Mage_Core_Exception $e) {
				}
			}
		}

		return $this;
	}

	public function salesQuoteSubmitSuccess($observer)
	{
		$helper = Mage::helper('giftcard');
		if ($helper->getConfig('general/order_item_status') == Mage_Sales_Model_Order_Item::STATUS_INVOICED) {
			return $this;
		}
		$order = $observer->getEvent()->getOrder();

		foreach ($order->getAllItems() as $item) {
			if ($item->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
				$this->_generateGiftcard($item, $order);
			}
		}

		return $this;
	}

	public function salesOrderSaveAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();

		if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {
			foreach ($order->getAllItems() as $item) {
				if ($item->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
					$this->_activeGiftcard($item, $item->getQtyOrdered(), true);
				}
			}
		}
	}

	public function salesOrderCancelAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();

		/** Gift Card Product */
		foreach ($order->getAllItems() as $item) {
			if ($item->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
				$this->_refundGiftcardProduct($item, $order);
			}
		}

		/** Gift Card Code */
		$this->_refundGiftCard($order);

		return $this;
	}

	public function salesOrderInvoiceSaveAfter($observer)
	{
		$invoice = $observer->getEvent()->getInvoice();
		$order   = $invoice->getOrder();

		$isActive = ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID);

		foreach ($invoice->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();
			if ($orderItem->isDummy()) {
				continue;
			}
			if (($orderItem->getProductType() != Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) ||
				$orderItem->getParentItemId()
			) {
				continue;
			}

			$this->_generateGiftcard($orderItem, $order, $isActive, $item->getQty());
		}

		return $this;
	}

	public function salesOrderCreditmemoSaveAfter($observer)
	{
		$creditmemo = $observer->getEvent()->getCreditmemo();
		$order      = $creditmemo->getOrder();
		foreach ($creditmemo->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();
			if ($orderItem->isDummy()) {
				continue;
			}
			if (($orderItem->getProductType() != Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) ||
				$orderItem->getParentItemId()
			) {
				continue;
			}

			$this->_refundGiftcardProduct($orderItem, $order, $item->getQty());
		}

		/** Refund Gift Card Used for order */
		$this->_refundGiftCard($creditmemo, false);

		return $this;
	}

	public function salesOrderShipmentSaveAfter($observer)
	{
		$shipment = $observer->getEvent()->getShipment();
		foreach ($shipment->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();
			if ($orderItem->getProductType() == Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
				$giftCard = Mage::getModel('giftcard/giftcard')->getCollection()
					->addFieldToFilter('item_id', $orderItem->getId())
					->addFieldToFilter('active', Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE)
					->addFieldToFilter('status', Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE);

				foreach ($giftCard as $gift) {
					if (Mage::helper('giftcard')->getConfig('email/purchase_email') &&
						!$this->getRecipientEmail()
					) {
						$gift->sendPurchaserEmail();
					}
				}
			}
		}

		return $this;
	}

	protected function _generateGiftcard($item, $order, $isActive = false, $qty = null)
	{
		$options    = $item->getProductOptions();
		$buyRequest = $options['info_buyRequest'];
		if (!isset($buyRequest['giftcard'])) {
			Mage::log(Mage::helper('giftcard')->__('Cannot create gift card from gift product. Item id #%s', $item->getId()));

			return $this;
		}

		$giftRequest = $buyRequest['giftcard'];
		if (!isset($giftRequest['giftcard_code'])) {
			$giftRequest['giftcard_code'] = array();
		}

		if (sizeof($giftRequest['giftcard_code']) == $item->getQtyOrdered()) {
			$this->_activeGiftcard($item, $qty);
		} else {
			if (isset($giftRequest['giftcard_amount']) && $giftRequest['giftcard_amount']) {
				$product     = $item->getProduct();
				$prepareData = array_merge($giftRequest, array(
					'amount'                 => $giftRequest['giftcard_amount'],
					'conditions_serialized'  => $product->getData('giftcard_conditions_serialized'),
					'actions_serialized'     => $product->getData('giftcard_actions_serialized'),
					'status'                 => Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE,
					'active'                 => $isActive ? Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE : Magegiant_GiftCard_Model_Giftcard::STATE_INACTIVE,
					'website_id'             => Mage::app()->getStore($item->getStoreId())->getWebsiteId(),
					'store_id'               => $item->getStoreId(),
					'order_id'               => $order->getId(),
					'order_increment_id'     => $order->getIncrementId(),
					'item_id'                => $item->getId(),
					'expire'                 => Mage::helper('giftcard')->getConfigData($product, 'expire'),
					'pattern'                => Mage::helper('giftcard')->getConfigData($product, 'pattern'),
					'conditions_description' => $product->getData('conditions_description'),
					'name'                   => $product->getName()
				));

				if (!in_array('sender_name', $prepareData) || !$prepareData['sender_name']) {
					$prepareData['sender_name'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
				}
				if (!in_array('sender_email', $prepareData) || !$prepareData['sender_email']) {
					$prepareData['sender_email'] = $order->getCustomerEmail();
				}

				$container = new Varien_Object($prepareData);
				Mage::dispatchEvent('giftcard_generate_product_prepare_data', array(
					'container' => $container,
					'product'   => $product,
					'request'   => $giftRequest,
					'item'      => $item,
					'order'     => $order
				));
				$prepareData = $container->getData();

				if (is_null($qty)) {
					$qty = $item->getQtyOrdered() - sizeof($giftRequest['giftcard_code']);
				} else {
					$qty = min($qty, $item->getQtyOrdered() - sizeof($giftRequest['giftcard_code']));
				}
				while ($qty--) {
					try {
						$giftCard = Mage::getModel('giftcard/giftcard')
							->setOrder($order)
							->addData($prepareData)
							->setData('allow_send_email', true)
							->save();

						$giftRequest['giftcard_code'][] = $giftCard->getCode();
					} catch (Exception $e) {
						Mage::logException($e);
					}
				}

				$buyRequest['giftcard']     = $giftRequest;
				$options['info_buyRequest'] = $buyRequest;

				$item->setProductOptions($options)
					->save();
			}
		}

		return $this;
	}

	protected function _refundGiftcardProduct($item, $order, $qty = null)
	{
		$giftRefunds = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('item_id', $item->getId())
			->addFieldToFilter('status', array('neq' => Magegiant_GiftCard_Model_Giftcard::STATUS_REFUNDED))
			->setOrder('amount', 'desc');
		if ($qty) {
			$giftRefunds->setPageSize($qty);
		}

		foreach ($giftRefunds as $gift) {
			$gift->setAction(Magegiant_GiftCard_Model_History::ACTION_UPDATED)
				->setHistoryDetail(Mage::helper('giftcard')->__('Order Refunded #%s', $order->getIncrementId()))
				->refund();
		}

		return $this;
	}

	protected function _refundGiftCard($object, $isCancel = true)
	{
		$giftCards = $object->getGiftCards();
		if (!is_array($giftCards)) {
			try {
				$giftCards = Mage::helper('core')->jsonDecode($giftCards);
			} catch (Exception $e) {
				return $this;
			}
		}

		if (!Mage::helper('giftcard/calculation')->canRefundBackToGiftcard() || empty($giftCards)) {
			return $this;
		}

		$rateRefund  = 1;
		$allowRefund = array_sum($giftCards);

		if ($isCancel) {
			$historyDetail = Mage::helper('giftcard')->__('Order Cancelled #%s', $object->getIncrementId());
		} else {
			$maxRefund = $object->getGiftcardRefundAmount();
			if(is_null($maxRefund)){
				$data = Mage::app()->getRequest()->getPost('creditmemo');
				if(isset($data['giftcard_refund_amount'])){
					$maxRefund = $data['giftcard_refund_amount'];
				}
			}
			$rateRefund  = $maxRefund / $allowRefund;
			$allowRefund = $maxRefund;

			$historyDetail = Mage::helper('giftcard')->__('Order Refunded. Credit memo Id: #%s', $object->getIncrementId());
		}

		foreach ($giftCards as $id => $amount) {
			$card = Mage::getModel('giftcard/giftcard')->load(Mage::app()->getLocale()->getNumber($id));
			if ($card->getId()) {
				try {
					$amountRefund = $this->roundPrice($amount * $rateRefund, 'base', $object);
					$refundAmount = $card->getAmount() + min($amountRefund, $allowRefund);
					$card->setAmount($refundAmount)
						->setHistoryDetail($historyDetail)
						->save();
					$allowRefund -= $amountRefund;
				} catch (Exception $e) {
					Mage::logException($e);
				}
			}
		}

		return $this;
	}

	public function roundPrice($price, $type = 'regular', $object)
	{
		if ($price) {
			if (!isset($this->_calculators[$type])) {
				$this->_calculators[$type] = Mage::getModel('core/calculator', $object->getStore());
			}
			$price = $this->_calculators[$type]->deltaRound($price, false);
		}

		return $price;
	}

	protected function _activeGiftcard($item, $qty, $isOrderComplete = false)
	{
		$giftCard = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('item_id', $item->getId())
			->addFieldToFilter('active', Magegiant_GiftCard_Model_Giftcard::STATE_INACTIVE)
			->setPageSize($qty);

		foreach ($giftCard as $gift) {
			$gift->setAction(Magegiant_GiftCard_Model_History::ACTION_UPDATED)
				->setHistoryDetail(Mage::helper('giftcard')->__('Gift Card Active'))
				->setData('allow_send_email', true)
				->active();
		}

		return $this;
	}

	public function updateGiftcardStatus()
	{
		$yesterday = Mage::getModel('core/date')->date('Y-m-d', time() - 86400);

		$giftCardModel      = Mage::getModel('giftcard/giftcard');
		$giftcardCollection = $giftCardModel->getCollection()
			->addFieldToFilter('status', Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE)
			->addFieldToFilter('active', Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE)
			->addFieldToFilter('expired_at', array('notnull' => true))
			->addFieldToFilter('expired_at', array('to' => $yesterday));

		if ($giftcardCollection->getSize()) {
			$giftCardModel->setHistoryDetail(Mage::helper('giftcard')->__('Gift Code Expired.'))
				->expire($giftcardCollection);
		}

		$this->sendReminderEmail();

		return $this;
	}

	public function sendReminderEmail()
	{
		$helper = Mage::helper('giftcard');
		if ($helper->getConfig('email/reminder_email')) {
			$reminderDay = $helper->getConfig('email/reminder_day');

			$currentDate    = Zend_Date::now()
				->toString(Varien_Date::DATE_INTERNAL_FORMAT);
			$expirationDate = Zend_Date::now()
				->addDate($reminderDay)
				->toString(Varien_Date::DATE_INTERNAL_FORMAT);

			$giftcardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
				->addFieldToFilter('status', Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE)
				->addFieldToFilter('active', Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE)
				->addFieldToFilter('reminder_sent', false)
				->addFieldToFilter('expired_at', array('notnull' => true))
				->addFieldToFilter('expired_at', array(
					'from' => $currentDate,
					'to'   => $expirationDate
				));

			foreach ($giftcardCollection as $giftcard) {
				try {
					$giftcard->sendReminderEmail();
				} catch (Exception $e) {
					Mage::logException($e);
				}
			}
		}

		return $this;
	}

	public function sendScheduleEmail()
	{
		$currentDate = Mage::getModel('core/date')->date('Y-m-d');

		$giftcardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('status', Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE)
			->addFieldToFilter('active', Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE)
			->addFieldToFilter('email_sent', false)
			->addFieldToFilter('schedule_at', array('notnull' => true))
			->addFieldToFilter('schedule_at', array('from' => $currentDate, 'to' => $currentDate));

		foreach ($giftcardCollection as $giftcard) {
			try {
				$giftcard->sendEmail();
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}

		return $this;
	}
}