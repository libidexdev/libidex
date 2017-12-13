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

class Magegiant_GiftCard_Block_Email_Template extends Magegiant_GiftCard_Block_Abstract
{
	public function getExpiredDay($card)
	{
		$expirationDateLocale = Mage::app()->getLocale()->date($card->getExpiredAt(), Varien_Date::DATE_INTERNAL_FORMAT, null, false);
		$currentDateLocale    = Mage::app()->getLocale()->date(null, null, null, false);

		$expirationDate = date_create($expirationDateLocale->toString(Varien_Date::DATE_INTERNAL_FORMAT));
		$currentDate    = date_create($currentDateLocale->toString(Varien_Date::DATE_INTERNAL_FORMAT));

		$date = date_diff($expirationDate, $currentDate)->format('%a');

		$date .= ($date > 1) ? ' days' : ' day';

		return $date;
	}

	public function getExpiredAtInStore($card)
	{
		$expirationDate = Mage::app()->getLocale()->date($card->getExpiredAt(), Varien_Date::DATE_INTERNAL_FORMAT, null, false);

		$format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

		return $expirationDate->toString($format);
	}
}