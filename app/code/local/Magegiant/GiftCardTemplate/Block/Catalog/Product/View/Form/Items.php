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
class Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Form_Items extends Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Form
{

    public function getColumnSpan()
    {
        if (version_compare(Mage::getVersion(), '1.9.0.0', '>=')) {
            return 2;
        }

        return 3;
    }

    /**
     * @return $this
     */
    public function getAllItems()
    {
        $items      = array();
        $designType = $this->getDesignType();
        if ($designType) {
            $designIds = explode(',', $designType);
            $group     = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            $websiteId = Mage::app()->getWebsite()->getId();
            $items     = Mage::getResourceModel('giftcardtemplate/design_items_collection')
                ->getAvailableItems()
                ->getAllItemsByDesignIds($designIds, $group, $websiteId)
                ->addFormatToFilter($this->getFormatType());
        }

        return $items;
    }
}