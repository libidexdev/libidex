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

class Magegiant_GiftCard_Model_Source_Product_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

	public function getAllOptions()
	{
		$result = array();
		foreach ($this->_getValues() as $k => $v) {
			$result[] = array(
				'value' => $k,
				'label' => $v,
			);
		}

		return $result;
	}

	public function getOptionLabel($type)
	{
		$types = $this->_getValues();
		if (isset($types[$type])) {
			return $types[$type];
		}

		return '';
	}

	protected function _getValues()
	{
		return array(
			Magegiant_GiftCard_Model_Giftcard::TYPE_VIRTUAL  => Mage::helper('giftcard')->__('Virtual'),
			Magegiant_GiftCard_Model_Giftcard::TYPE_PHYSICAL => Mage::helper('giftcard')->__('Physical'),
			Magegiant_GiftCard_Model_Giftcard::TYPE_COMBINED => Mage::helper('giftcard')->__('Combined'),
		);
	}
}
