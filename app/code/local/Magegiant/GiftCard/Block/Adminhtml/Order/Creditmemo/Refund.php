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
class Magegiant_GiftCard_Block_Adminhtml_Order_Creditmemo_Refund extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Form
{
	public function getGiftcardRefundAmount()
	{
		return abs($this->getCreditmemo()->getBaseGiftcardAmount());
	}

	public function allowRefund()
	{
		if (Mage::helper('giftcard')->getConfig('redeem/calculation/refund') == Magegiant_GiftCard_Model_Source_Order_Refund::REFUND_ZERO) {
			return false;
		}

		return $this->getGiftcardRefundAmount();
	}
}
