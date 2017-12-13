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
class Magegiant_GiftCard_Block_Totals_Order extends Mage_Core_Block_Template
{
	/**
	 * add discount value into order total
	 *
	 */
	public function initTotals()
	{
		$totalsBlock = $this->getParentBlock();
		$order       = $totalsBlock->getOrder();
		if (abs($order->getGiftcardAmount()) >= 0.0001) {
			$this->setOrder($order);
			$totalsBlock->addTotal(new Varien_Object(array(
				'code'       => 'giftcard',
				'block_name' => $this->getNameInLayout()
			)), 'tax');
		}

		Mage::dispatchEvent('giftcard_order_total_view', array(
			'totals_block' => $totalsBlock,
			'source'       => $order
		));

		return $this;
	}

	public function getLabel($code = null)
	{
		if (is_null($code)) {
			return $this->__('Gift Card');
		}

		return $this->__('Gift Card (%s)', $code);
	}

	public function getValueFormated($value = null)
	{
		if (is_null($value)) {
			$value = $this->getOrder()->getGiftcardAmount();
		}

		return $this->getOrder()->formatPrice($value);
	}

	public function getGiftCards()
	{
		$giftCards = $this->getOrder()->getGiftCards();
		if (!is_array($giftCards)) {
			try {
				$giftCards = Mage::helper('core')->jsonDecode($giftCards);
			} catch (Exception $e){
				return array();
			}
		}

		$giftCardsCollection = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('giftcard_id', array('in' => array_keys($giftCards)));
		foreach ($giftCardsCollection as $giftcard) {
			$giftCards[$giftcard->getId()] = array(
				'code'  => $giftcard->getCode(),
				'value' => $giftCards[$giftcard->getId()]
			);
		}

		return $giftCards;
	}

	public function getLabelProperties()
	{
		return $this->getParentBlock()->getLabelProperties();
	}

	public function getValueProperties()
	{
		return $this->getParentBlock()->getValueProperties();
	}
}
