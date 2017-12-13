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
 * @package     Magegiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardtemplate Block
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Popup_Upload extends Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Abstract
{
    public function getMaxFileSize()
    {
        return Mage::helper('giftcardtemplate/config')->getMaxFileSize();
    }

    /**
     * @return string
     */
    public function getDefaultFrame()
    {
        return $this->getImgSrc('upload/default.jpg', true);
    }

    /**
     * @return string
     */
    public function getUploadImgUrl()
    {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();

        return Mage::getUrl('giftcardtemplate/processor/uploadImage', array('_secure' => $isSecure));
    }


}