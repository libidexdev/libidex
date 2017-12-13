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

class Magegiant_GiftCard_Model_Source_Order_Refund extends Mage_Core_Model_Abstract
{
	const REFUND_ZERO = '0';
	const REFUND_TO_CARD = '1';


	public function toOptionArray()
	{
		$optionsArray = array();
		foreach ($this->toOptionHash() as $code => $label) {
			$optionsArray[] = array(
				'value' => $code,
				'label' => $label
			);
		}

		return $optionsArray;
	}

	public function toOptionHash()
	{
		$options = new Varien_Object(array(
			self::REFUND_ZERO    => Mage::helper('giftcard')->__('Don\'t allow'),
			self::REFUND_TO_CARD => Mage::helper('giftcard')->__('Back to Gift Card amount'),
		));

		Mage::dispatchEvent('giftcard_source_order_refund', array(
			'options' => $options
		));

		return $options->getData();
	}
}
