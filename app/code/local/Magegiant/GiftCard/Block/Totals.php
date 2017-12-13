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
class Magegiant_GiftCard_Block_Totals extends Mage_Core_Block_Template
{
	/**
	 * add discount value into order total
	 *
	 */
	public function initTotals()
	{
		$totalsBlock = $this->getParentBlock();
		$source      = $totalsBlock->getSource();
		if (abs($source->getGiftcardAmount()) >= 0.0001) {
			$totalsBlock->addTotal(new Varien_Object(array(
				'code'       => 'giftcard',
				'label'      => $this->__('Gift Card'),
				'value'      => $source->getGiftcardAmount(),
				'base_value' => $source->getBaseGiftcardAmount(),
			)), 'tax');
		}

		Mage::dispatchEvent('giftcard_order_total_view', array(
			'totals_block' => $totalsBlock
		));

		return $this;
	}
}
