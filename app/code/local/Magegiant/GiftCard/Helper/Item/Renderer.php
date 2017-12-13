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

class Magegiant_GiftCard_Helper_Item_Renderer extends Magegiant_GiftCard_Helper_Data
{
	public function getOptionValue($code, $value)
	{
		if ($code == 'amount') {
			return Mage::helper('core')->formatPrice($value);
		}

		if (is_array($value)) {
			return implode(', ', $value);
		}

		return $this->escapeHtml($value);
	}

	public function groupField(&$array, array $fields)
	{
		foreach ($fields as $field) {
			$fieldName  = $field . '_name';
			$fieldEmail = $field . '_email';
			if (array_key_exists($fieldName, $array) && $array[$fieldName]) {
				$to = $this->getAndUnset($array, $fieldName);
				if (array_key_exists($fieldEmail, $array) && $array[$fieldEmail]) {
					$to .= ' <' . $this->getAndUnset($array, $fieldEmail) . '>';
				}
				$array = array($field => $to) + $array;
			}
		}

		return $this;
	}

	public function getAndUnset(&$array, $field)
	{
		$value = $array[$field];
		unset($array[$field]);

		return $value;
	}
}