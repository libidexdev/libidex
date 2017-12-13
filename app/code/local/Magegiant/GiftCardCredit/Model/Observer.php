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
 * GiftCardCredit Observer Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardCredit_Model_Observer
{
	public function paypalPrepareLineItems($observer)
	{
		if ($paypalCart = $observer->getPaypalCart()) {
			$salesEntity = $paypalCart->getSalesEntity();

			$baseDiscount = abs($salesEntity->getBaseGiftcardCreditAmount());
			if ($baseDiscount > 0.0001) {
				$paypalCart->updateTotal(
					Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, (float)$baseDiscount, Mage::helper('giftcard')->__('Credit Discount')
				);
			}
		}

		return $this;
	}

	public function setStatusRedeemToGiftCard($observer)
	{
		$statuses = $observer->getEvent()->getStatuses();
		$statuses->setData(
			Magegiant_GiftCardCredit_Model_Giftcard::STATUS_REDEEM,
			Mage::helper('giftcardcredit')->__('Redeemed')
		);

		return $this;
	}

	public function addDataToGenerateGiftcard($observer)
	{
		$data    = $observer->getEvent()->getContainer();
		$product = $observer->getEvent()->getProduct();

		$data->setData('allow_redeem', Mage::helper('giftcard')->getConfigData($product, 'allow_redeem'));

		return $this;
	}

	public function addActionHistoryToGiftCard($observer)
	{
		$actions = $observer->getEvent()->getActions();

		$actions->setData(
			Magegiant_GiftCardCredit_Model_Giftcard::ACTION_REDEEMED,
			Mage::helper('giftcardcredit')->__('Redeemed')
		);

		return $this;
	}

	public function initTotals($observer)
	{
		if (!Mage::helper('giftcard')->isEnabled()) {
			return $this;
		}
		$totalsBlock = $observer->getEvent()->getTotalsBlock();
		$source      = $totalsBlock->getSource();
		if (abs($source->getGiftcardCreditAmount()) >= 0.0001) {
			$totalsBlock->addTotal(new Varien_Object(array(
				'code'       => 'giftcard_credit',
				'label'      => Mage::helper('giftcardcredit')->__('Credit Discount'),
				'value'      => $source->getGiftcardCreditAmount(),
				'base_value' => $source->getBaseGiftcardCreditAmount(),
			)), 'tax');
		}

		return $this;
	}

	public function salesQuoteSubmitSuccess($observer)
	{
		$order = $observer->getEvent()->getOrder();
		if (!$order->getCustomerIsGuest() && $order->getBaseGiftcardCreditAmount()) {
			$account = Mage::helper('giftcardcredit')->getCreditAccount($order->getCustomerId());
			$account->spend($order->getBaseGiftcardCreditAmount());

			Mage::helper('giftcard')->getSession()->unsUseCredit()
				->unsGiftcardCreditAmount();
		}

		return $this;
	}

	public function salesOrderCancelAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();

		/** Gift Card Code */
		$this->_refundGiftCard($order);

		return $this;
	}

	public function salesOrderCreditmemoSaveAfter($observer)
	{
		$creditmemo = $observer->getEvent()->getCreditmemo();
		$order      = $creditmemo->getOrder();

		if (!$order->getCustomerIsGuest() && $creditmemo->getBaseGiftcardCreditAmount()) {
			$account = Mage::helper('giftcardcredit')->getCreditAccount($order->getCustomerId());
			$account->refund($creditmemo->getBaseGiftcardCreditAmount());
		}

		$this->_refundGiftCard($creditmemo, false);

		return $this;
	}

	protected function _refundGiftCard($object, $isCancel = true)
	{
		$giftCards = $object->getGiftCards();
		if (!is_array($giftCards)) {
			try{
				$giftCards = Mage::helper('core')->jsonDecode($giftCards);
			} catch (Exception $e) {
				return $this;
			}
		}

		if (!Mage::helper('giftcardcredit/calculation')->canRefundToCredit() || empty($giftCards)) {
			return $this;
		}

		$allowRefund = array_sum($giftCards);
		if (!$isCancel) {
			$allowRefund = $object->getGiftcardRefundAmount();
		}

		if ($allowRefund) {
			if ($object->getCustomerId()) {
				$account = Mage::helper('giftcardcredit')->getCreditAccount($object->getCustomerId());
				$account->refund($allowRefund);
			}
		}

		return $this;
	}
}