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
class Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Abstract extends Mage_Core_Block_Template
{
    protected $_currentFormat;
    protected $_currentDesign;
    protected $_currentMode = 'grid';
    protected $_product;

    /**
     * check giant points system is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::helper('giftcardtemplate/config')->isEnabled();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    public function getEmbedVideoByUrl($url)
    {
        if ($this->getIsSecured()) {
            $baseLink = 'https://www.youtube.com/embed/';
        } else {
            $baseLink = 'http://www.youtube.com/embed/';
        }
        $baseLink .= Mage::helper('giftcardtemplate')->getVideoIdByUrl($url);

        return $baseLink;
    }

    public function getIsSecured()
    {
        return Mage::app()->getStore()->isCurrentlySecure();

    }

    protected function _getAllDesign($ids = array())
    {
        $customer  = Mage::getSingleton('customer/session')->getCustomer();
        $websiteId = Mage::app()->getWebsite()->getId();
        $designs   = Mage::getModel('giftcardtemplate/design')->getAllDesigns($customer->getGroupId(), $websiteId, $ids);

        return $designs;
    }

    public function getFormatsByDesign($designIds)
    {

        $designsIds = $this->_getAllDesign($designIds)->getAllIds();
        $formats    = Mage::getModel('giftcardtemplate/design_items')->getCollection()
            ->getAllFormatsByDesignIds($designsIds);
        $array      = array();
        foreach ($formats as $format) {
            $array[] = $format->getFormatId();
        }

        return $array;

    }

    /**
     * @return int|mixed
     * @throws Exception
     */
    public function getFormatType()
    {
        if ($formatType = $this->getRequest()->getParam('format_type')) {
            $this->_currentFormat = $formatType;

        } else if ($this->getProduct()) {
            $designIds            = explode(',', $this->getProduct()->getData('giftcard_design_ids'));
            $formats              = $this->getFormatsByDesign($designIds);
            $this->_currentFormat = isset($formats[0]) ? $formats[0] : '';

        }

        return $this->_currentFormat;
    }

    /**
     * @return int|mixed
     * @throws Exception
     */
    public function getDesignType()
    {
        if ($this->getProduct()) {
            $productDesignIds     = explode(',', $this->getProduct()->getData('giftcard_design_ids'));
            $designIds            = $this->_getAllDesign($productDesignIds)->getAllIds();
            $this->_currentDesign = implode(',', $designIds);
        } else if ($designType = $this->getRequest()->getParam('design_type')) {
            $this->_currentDesign = $designType;
        }

        return $this->_currentDesign;
    }

    /**
     * @return mixed|string
     * @throws Exception
     */
    public function getModeType()
    {
        if ($modeType = $this->getRequest()->getParam('mode_type')) {
            $this->_currentMode = $modeType;
        }

        return $this->_currentMode;
    }

    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('current_product');
        }

        return $this->_product;
    }

    /**
     * Check item type is Custom Uploads
     *
     * @param $item
     * @return bool
     */
    public function checkIsCustomUpload($item)
    {
        $formatId = $item->getFormatId();
        if ($formatId == Magegiant_GiftCardTemplate_Model_Format_Options::FORMAT_YOUR_PHOTO) {
            return true;
        }

        return false;
    }

    public function checkIsVideo($item)
    {
        $formatId = $item->getFormatId();
        if ($formatId == Magegiant_GiftCardTemplate_Model_Format_Options::FORMAT_ANIMATED) {
            return true;
        }

        return false;
    }

    public function isUploadFormat()
    {
        return $this->getFormatType() == Magegiant_GiftCardTemplate_Model_Format_Options::FORMAT_YOUR_PHOTO;
    }

    /**
     * @param $name
     * @return string
     */
    public function getImgSrc($name, $isDefault = false)
    {
        if ($isDefault) {
            $mediaPath = 'https://raw.githubusercontent.com/magegiant/giftcardtemplate/master/media/magegiant/giftcardtemplate/images/';
        } else {
            $mediaPath = Mage::helper('giftcardtemplate')->getMediaUrl('magegiant/giftcardtemplate/images/');
        }

        return $mediaPath . $name;

    }

    public function getBaseImageUrl()
    {
        return Mage::helper('giftcardtemplate')->getMediaUrl('magegiant/giftcardtemplate/images/upload/');
    }
}