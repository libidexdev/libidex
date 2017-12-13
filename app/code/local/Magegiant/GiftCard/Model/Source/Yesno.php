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

class Magegiant_GiftCard_Model_Source_Yesno extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
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

	protected function _getValues()
	{
		return array(
			0 => Mage::helper('giftcard')->__('No'),
			1 => Mage::helper('giftcard')->__('Yes'),
		);
	}
}
