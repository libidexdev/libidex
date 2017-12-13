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
class Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Form extends Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Abstract
{

    public function getCurrentItem()
    {
        $item      = null;
        $designIds = explode(',', $this->getDesignType());
        if (!empty($designIds)) {
            $item = Mage::getResourceModel('giftcardtemplate/design_items_collection')
                ->addDesignToFilter($designIds)->getFirstItem();
        }

        return $item;

    }

    /**
     * @return string
     */
    public function getChangeFormatUrl()
    {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();

        return Mage::getUrl('giftcardtemplate/processor/changeFormFormat', array('_secure' => $isSecure));
    }

    /**
     * @return string
     */
    public function getRemoveImageUrl()
    {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();

        return Mage::getUrl('giftcardtemplate/processor/removeImage', array('_secure' => $isSecure));
    }

    /**
     * prepare block's layout
     *
     * @return Magegiant_GiftCardTemplate_Block_Giftcardtemplate
     */
    public function getBlockActions()
    {
        $blocks       = array();
        $blockMapping = Mage::helper('giftcardtemplate/block')->getBlockActions();
        foreach ($blockMapping as $action => $blockName) {
            $blocks[$action] = array_keys((array)$blockName);
        }

        return Mage::helper('core')->jsonEncode($blocks);
    }

    /**
     *
     */
    public function getActionPattern()
    {
        $actionPattern = '/giftcardtemplate\/processor\/([^\/]+)\//';

        return $actionPattern;
    }

    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
        }

        return $this->getSkinUrl($this->_data['logo_src']);
    }

    public function getBlockSections()
    {
        $blocks = Mage::helper('giftcardtemplate/block')->getBlockSections();

        return Mage::helper('core')->jsonEncode($blocks);
    }

    public function getCurrentDesign()
    {
        $designIds = explode(',', $this->getDesignType());

        return $this->_getAllDesign($designIds)->getFirstItem();
    }

    public function getPattern()
    {
        $pattern = Mage::helper('giftcard')->getConfigData($this->getProduct(), 'pattern');
        $code    = Mage::helper('giftcard')->generateCode($pattern);

        return preg_replace('/[\w\d]/', 'X', $code);

    }
}