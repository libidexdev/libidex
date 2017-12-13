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
class Magegiant_GiftCard_Model_Total_Creditmemo_Giftcard extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$creditmemo->setGiftcardAmount(0);
		$creditmemo->setBaseGiftcardAmount(0);

		$order = $creditmemo->getOrder();

		$totalDiscountAmount     = 0;
		$baseTotalDiscountAmount = 0;

		$giftCards = array();

		/**
		 * Calculate how much shipping discount should be applied
		 * basing on how much shipping should be refunded.
		 */
		$baseShippingAmount = $creditmemo->getBaseShippingAmount();
		if ($baseShippingAmount) {
			$baseShippingDiscount    = $baseShippingAmount * $order->getBaseGiftcardShippingAmount() / $order->getBaseShippingAmount();
			$shippingDiscount        = $order->getShippingAmount() * $baseShippingDiscount / $order->getBaseShippingAmount();
			$totalDiscountAmount     = $totalDiscountAmount + $shippingDiscount;
			$baseTotalDiscountAmount = $baseTotalDiscountAmount + $baseShippingDiscount;
		}

		foreach ($creditmemo->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();

			if ($orderItem->isDummy()) {
				continue;
			}

			$orderItemDiscount     = (float)$orderItem->getGiftcardInvoiced();
			$baseOrderItemDiscount = (float)$orderItem->getBaseGiftcardInvoiced();

			$orderItemQty = $orderItem->getQtyInvoiced();

			if ($orderItemDiscount && $orderItemQty) {
				$discount     = $orderItemDiscount - $orderItem->getGiftcardRefunded();
				$baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseGiftcardRefunded();
				if (!$item->isLast()) {
					$availableQty = $orderItemQty - $orderItem->getQtyRefunded();
					$discount     = $creditmemo->roundPrice(
						$discount / $availableQty * $item->getQty(), 'regular', true
					);
					$baseDiscount = $creditmemo->roundPrice(
						$baseDiscount / $availableQty * $item->getQty(), 'base', true
					);
				}

				$orderItem->setGiftcardRefunded($orderItem->getGiftcardRefunded() + $discount);
				$orderItem->setBaseGiftcardRefunded($orderItem->getBaseGiftcardRefunded() + $baseDiscount);

				$totalDiscountAmount += $discount;
				$baseTotalDiscountAmount += $baseDiscount;

				$itemGiftCards         = Mage::helper('core')->jsonDecode($orderItem->getGiftCards());
				$itemGiftCardsRefunded = Mage::helper('core')->jsonDecode($orderItem->getGiftCardsRefunded());
				$totalCardAmount       = array_sum($itemGiftCards);
				$tempCards             = array();
				foreach ($itemGiftCards as $key => $value) {
					if ($item->getQty() == ($orderItem->getQtyOrdered() - $orderItem->getQtyRefunded())) {
						$refundAmount                = isset($itemGiftCardsRefunded[$key . '_prefix']) ? $itemGiftCardsRefunded[$key . '_prefix'] : 0;
						$tempCards[$key . '_prefix'] = $value - $refundAmount;
					} else {
						$tempCards[$key . '_prefix'] = $creditmemo->roundPrice(
							$baseDiscount / $totalCardAmount * $value, 'giftcard', true
						);
					}
				}
				$itemGiftCardsRefunded = $this->_calculateArray($itemGiftCardsRefunded, $tempCards);
				$orderItem->setGiftCardsRefunded(Mage::helper('core')->jsonEncode($itemGiftCardsRefunded));
				$giftCards = $this->_calculateArray($giftCards, $tempCards);
			}
		}

		$creditmemo->setGiftCards($giftCards);

		$creditmemo->setGiftcardAmount(-$totalDiscountAmount);
		$creditmemo->setBaseGiftcardAmount(-$baseTotalDiscountAmount);

		$creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
		$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);

		Mage::dispatchEvent('giftcard_collect_creditmemo_after', array(
			'creditmemo' => $creditmemo,
			'total'      => $this
		));

		return $this;
	}

	public function isLast($creditmemo)
	{
		foreach ($creditmemo->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();
			if ($orderItem->isDummy()) {
				continue;
			}
			if (!$item->isLast()) {
				return false;
			}
		}

		return true;
	}

	protected function _calculateArray($arraySub, $arrayPre, $operator = 1)
	{
		if (!is_array($arraySub)) {
			$arraySub = array();
		}
		if (!is_array($arrayPre)) {
			$arrayPre = array();
		}

		$result = array_intersect_key($arraySub, $arrayPre);
		foreach ($result as $key => $value) {
			$result[$key] += $operator * $arrayPre[$key];
		}

		return array_merge($arraySub, $arrayPre, $result);
	}
}
