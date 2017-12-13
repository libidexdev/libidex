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

class Magegiant_GiftCard_Block_Account_Giftcard_View extends Magegiant_GiftCard_Block_Abstract
{
	public function getGiftcardInformation()
	{
		return Mage::registry('giftcard_view_information');
	}

	public function getGiftcardHistory()
	{
		return Mage::registry('giftcard_view_history');
	}
}