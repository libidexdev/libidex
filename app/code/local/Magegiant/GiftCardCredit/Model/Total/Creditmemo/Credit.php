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
class Magegiant_GiftCardCredit_Model_Total_Creditmemo_Credit
{
	public function collect($observer){
		$creditmemo = $observer->getEvent()->getCreditmemo();

		$creditmemo->setGiftcardCreditAmount(0);
		$creditmemo->setBaseGiftcardCreditAmount(0);

		$order = $creditmemo->getOrder();

		$totalDiscountAmount     = 0;
		$baseTotalDiscountAmount = 0;

		/**
		 * Calculate how much shipping discount should be applied
		 * basing on how much shipping should be refunded.
		 */
		$baseShippingAmount = $creditmemo->getBaseShippingAmount();
		if ($baseShippingAmount) {
			$baseShippingDiscount    = $baseShippingAmount * $order->getBaseGiftcardCreditShippingAmount() / $order->getBaseShippingAmount();
			$shippingDiscount        = $order->getShippingAmount() * $baseShippingDiscount / $order->getBaseShippingAmount();
			$totalDiscountAmount     = $totalDiscountAmount + $shippingDiscount;
			$baseTotalDiscountAmount = $baseTotalDiscountAmount + $baseShippingDiscount;
		}

		foreach ($creditmemo->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();

			if ($orderItem->isDummy()) {
				continue;
			}

			$orderItemDiscount     = (float)$orderItem->getGiftcardCreditInvoiced();
			$baseOrderItemDiscount = (float)$orderItem->getBaseGiftcardCreditInvoiced();

			$orderItemQty = $orderItem->getQtyInvoiced();

			if ($orderItemDiscount && $orderItemQty) {
				$discount     = $orderItemDiscount - $orderItem->getGiftcardCreditRefunded();
				$baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseGiftcardCreditRefunded();
				if (!$item->isLast()) {
					$availableQty = $orderItemQty - $orderItem->getQtyRefunded();
					$discount     = $creditmemo->roundPrice(
						$discount / $availableQty * $item->getQty(), 'regular', true
					);
					$baseDiscount = $creditmemo->roundPrice(
						$baseDiscount / $availableQty * $item->getQty(), 'base', true
					);
				}

				$orderItem->setGiftcardCreditRefunded($orderItem->getGiftcardCreditRefunded() + $discount);
				$orderItem->setBaseGiftcardCreditRefunded($orderItem->getBaseGiftcardCreditRefunded() + $baseDiscount);

				$totalDiscountAmount += $discount;
				$baseTotalDiscountAmount += $baseDiscount;
			}
		}

		$creditmemo->setGiftcardCreditAmount(-$totalDiscountAmount);
		$creditmemo->setBaseGiftcardCreditAmount(-$baseTotalDiscountAmount);

		$creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
		$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);

		return $this;
	}
}