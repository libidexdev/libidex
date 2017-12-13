<?php

/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magegiant.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement.html
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit_Tab_Renderer_SortOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function _getInputValueElement(Varien_Object $row)
    {
        $designId = $this->getRequest()->getParam('id');
        $detail   = Mage::getModel('giftcardtemplate/design_items_detail')->getCollection()
            ->addFieldToFilter('design_id', $designId)
            ->addFieldToFilter('item_id', $row->getId())
            ->getFirstItem();

        return '<input style="text-align: right;" type="text" class="input-text '
        . $this->getColumn()->getValidateClass()
        . '" name="' . $this->getColumn()->getId() . '[' . $row->getId() . ']'
        . '" value="' . $detail->getSortOrder() . '"/>';
    }
}