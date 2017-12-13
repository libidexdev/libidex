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


class Magegiant_GiftCard_Model_Source_Box extends Mage_Core_Model_Abstract
{
	const BOX_COUPON = 0;
	const BOX_SEPARATE = 1;

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::BOX_COUPON,
                'label' => Mage::helper('giftcard')->__('Coupon Code Box')
            ),
            array(
                'value' => self::BOX_SEPARATE,
                'label' => Mage::helper('giftcard')->__('Separate Box')
            )
        );
    }
}
