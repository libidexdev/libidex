<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * GiftCardTemplate Status Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardTemplate_Model_Format_Options extends Varien_Object
{
    const FORMAT_STANDARD = 1;
    const FORMAT_ANIMATED = 2;
    const FORMAT_YOUR_PHOTO = 3;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::FORMAT_STANDARD   => Mage::helper('giftcardtemplate')->__('Images'),
            self::FORMAT_ANIMATED   => Mage::helper('giftcardtemplate')->__('Videos'),
            self::FORMAT_YOUR_PHOTO => Mage::helper('giftcardtemplate')->__('Frames'),
        );
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }

    public function getOptionLabel($key)
    {
        $options = self::getOptionArray();
        if (isset($options[$key])) {
            return $options[$key];
        }

        return false;
    }

    public function toOptionArray()
    {
        return self::getOptionHash();
    }

}