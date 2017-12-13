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

class Magegiant_GiftCard_Model_Total_Pdf_Giftcard extends Mage_Sales_Model_Order_Pdf_Total_Default
{
	public function getTotalsForDisplay()
	{
		$amount = $this->getOrder()->formatPriceTxt($this->getAmount());
		if ($this->getAmountPrefix()) {
			$amount = $this->getAmountPrefix() . $amount;
		}
		$label    = Mage::helper('giftcard')->__($this->getTitle()) . ':';
		$fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
		$total    = array(
			'amount'    => $amount,
			'label'     => $label,
			'font_size' => $fontSize
		);

		return array($total);
	}
}
