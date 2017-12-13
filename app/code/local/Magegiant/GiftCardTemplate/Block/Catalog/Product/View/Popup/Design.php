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
class Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Popup_Design extends Magegiant_GiftCardTemplate_Block_Catalog_Product_View_Abstract
{
    /**
     * prepare block's layout
     *
     * @return Magegiant_GiftCardTemplate_Block_Giftcardtemplate
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getDesignType()
    {
        return $this->getRequest()->getParam('design_type');
    }

    /**
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getAllDesign()
    {
        $designs = $this->_getAllDesign();
        if ($formatType = $this->getRequest()->getParam('format_type')) {
            $designs->addFormatToFilter($formatType);
        }

        return $designs;
    }

    /**
     * get change design url
     *
     * @return string
     */
    public function getChangeDesignUrl()
    {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();

        return Mage::getUrl('giftcardtemplate/processor/changeDesign', array('_secure' => $isSecure));
    }
}