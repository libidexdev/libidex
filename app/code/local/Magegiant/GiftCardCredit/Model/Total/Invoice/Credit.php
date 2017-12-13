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
class Magegiant_GiftCardCredit_Model_Total_Invoice_Credit
{
	public function collect($observer){
		$invoice = $observer->getEvent()->getInvoice();

		$invoice->setGiftcardCreditAmount(0);
		$invoice->setBaseGiftcardCreditAmount(0);

		$totalDiscountAmount     = 0;
		$baseTotalDiscountAmount = 0;

		/**
		 * Checking if shipping discount was added in previous invoices.
		 * So basically if we have invoice with positive discount and it
		 * was not canceled we don't add shipping discount to this one.
		 */
		$addShippingDicount = true;
		foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
			if ($previousInvoice->getGiftcardCreditAmount()) {
				$addShippingDicount = false;
			}
		}

		if ($addShippingDicount) {
			$totalDiscountAmount     = $totalDiscountAmount + $invoice->getOrder()->getGiftcardCreditShippingAmount();
			$baseTotalDiscountAmount = $baseTotalDiscountAmount + $invoice->getOrder()->getBaseGiftcardCreditShippingAmount();
		}

		foreach ($invoice->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();
			if ($orderItem->isDummy()) {
				continue;
			}

			$orderItemDiscount     = (float)$orderItem->getGiftcardCreditAmount();
			$baseOrderItemDiscount = (float)$orderItem->getBaseGiftcardCreditAmount();
			$orderItemQty          = $orderItem->getQtyOrdered();

			if ($orderItemDiscount && $orderItemQty) {
				$discount     = $orderItemDiscount - $orderItem->getGiftcardCreditInvoiced();
				$baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseGiftcardCreditInvoiced();

				if (!$item->isLast()) {
					$activeQty    = $orderItemQty - $orderItem->getQtyInvoiced();
					$discount     = $invoice->roundPrice($discount / $activeQty * $item->getQty(), 'regular', true);
					$baseDiscount = $invoice->roundPrice($baseDiscount / $activeQty * $item->getQty(), 'base', true);
				}

				$orderItem->setGiftcardCreditInvoiced($orderItem->getGiftcardCreditInvoiced() + $discount);
				$orderItem->setBaseGiftcardCreditInvoiced($orderItem->getBaseGiftcardCreditInvoiced() + $baseDiscount);

				$totalDiscountAmount += $discount;
				$baseTotalDiscountAmount += $baseDiscount;
			}
		}

		$invoice->setGiftcardCreditAmount(-$totalDiscountAmount);
		$invoice->setBaseGiftcardCreditAmount(-$baseTotalDiscountAmount);

		$invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);

		return $this;
	}
}