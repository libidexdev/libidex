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

class Magegiant_GiftCard_Model_Source_Options extends Mage_Core_Model_Abstract
{
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
			'amount'      => Mage::helper('giftcard')->__('Gift Card value'),
			'sender'      => Mage::helper('giftcard')->__('Sender'),
			'recipient'   => Mage::helper('giftcard')->__('Recipient'),
			'message'     => Mage::helper('giftcard')->__('Custom message'),
			'schedule_at' => Mage::helper('giftcard')->__('Send Date'),
		));

		Mage::dispatchEvent('giftcard_source_options_add', array(
			'options' => $options
		));

		return $options->getData();
	}
}
