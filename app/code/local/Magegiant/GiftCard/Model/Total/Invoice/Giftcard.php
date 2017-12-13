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
class Magegiant_GiftCard_Model_Total_Invoice_Giftcard extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$invoice->setGiftcardAmount(0);
		$invoice->setBaseGiftcardAmount(0);

		$totalDiscountAmount     = 0;
		$baseTotalDiscountAmount = 0;

		/**
		 * Checking if shipping discount was added in previous invoices.
		 * So basically if we have invoice with positive discount and it
		 * was not canceled we don't add shipping discount to this one.
		 */
		$addShippingDicount = true;
		foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
			if ($previousInvoice->getGiftcardAmount()) {
				$addShippingDicount = false;
			}
		}

		if ($addShippingDicount) {
			$totalDiscountAmount     = $totalDiscountAmount + $invoice->getOrder()->getGiftcardShippingAmount();
			$baseTotalDiscountAmount = $baseTotalDiscountAmount + $invoice->getOrder()->getBaseGiftcardShippingAmount();
		}

		foreach ($invoice->getAllItems() as $item) {
			$orderItem = $item->getOrderItem();
			if ($orderItem->isDummy()) {
				continue;
			}

			$orderItemDiscount     = (float)$orderItem->getGiftcardAmount();
			$baseOrderItemDiscount = (float)$orderItem->getBaseGiftcardAmount();
			$orderItemQty          = $orderItem->getQtyOrdered();

			if ($orderItemDiscount && $orderItemQty) {
				$discount     = $orderItemDiscount - $orderItem->getGiftcardInvoiced();
				$baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseGiftcardInvoiced();

				if (!$item->isLast()) {
					$activeQty    = $orderItemQty - $orderItem->getQtyInvoiced();
					$discount     = $invoice->roundPrice($discount / $activeQty * $item->getQty(), 'regular', true);
					$baseDiscount = $invoice->roundPrice($baseDiscount / $activeQty * $item->getQty(), 'base', true);
				}

				$orderItem->setGiftcardInvoiced($orderItem->getGiftcardInvoiced() + $discount);
				$orderItem->setBaseGiftcardInvoiced($orderItem->getBaseGiftcardInvoiced() + $baseDiscount);

				$totalDiscountAmount += $discount;
				$baseTotalDiscountAmount += $baseDiscount;
			}
		}

		$invoice->setGiftcardAmount(-$totalDiscountAmount);
		$invoice->setBaseGiftcardAmount(-$baseTotalDiscountAmount);

		$invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);

		Mage::dispatchEvent('giftcard_collect_invoice_after', array(
			'invoice' => $invoice,
			'total'   => $this
		));

		return $this;
	}

	public function isLast($invoice)
	{
		foreach ($invoice->getAllItems() as $item) {
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
}
