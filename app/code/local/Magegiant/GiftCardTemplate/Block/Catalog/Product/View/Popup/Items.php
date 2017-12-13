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
class Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Popup_Items extends Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Abstract
{

    /**
     * @return $this
     */
    public function getAllItems()
    {
        $items = Mage::getResourceModel('giftcardtemplate/design_items_collection')
            ->getAvailableItems();
        if ($this->getFormatType()) {
            $items->addFormatToFilter($this->getFormatType());
        }
        if ($this->getFormatType()) {
            $items->addFormatToFilter($this->getFormatType());
        }
        $designType = $this->getRequest()->getParam('design_type');
        $group      = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        $websiteId  = Mage::app()->getWebsite()->getId();
        $designIds  = $designType ? explode(',', $designType) : array();
        $clone      = clone $items->getSelect();
        $items->getAllItemsByDesignIds($designIds, $group, $websiteId);
        if (!$items->getSize()) {
            $items->getSelect()->reset()
                ->from(array('main_table' => new Zend_Db_Expr('(' . $clone->__toString() . ')')));
        }

        return $items;
    }

    public function getChangeModeUrl()
    {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();

        return Mage::getUrl('giftcardtemplate/processor/changeMode', array('_secure' => $isSecure));
    }


}