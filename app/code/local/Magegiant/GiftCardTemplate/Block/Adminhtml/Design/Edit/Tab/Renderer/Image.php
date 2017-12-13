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
class Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit_Tab_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getFormatId() == Magegiant_GiftCardTemplate_Model_Format_Options::FORMAT_ANIMATED) {
            $basePath  = '';
            $thumbFile = $row->getThumbFile();
        } else {
            if ($row->getIsDefault()) {
                $basePath = 'https://raw.githubusercontent.com/magegiant/giftcardtemplate/master/media/magegiant/giftcardtemplate/images/';
            } else {
                $basePath = Mage::helper('giftcardtemplate/upload')->getBaseImageUrl('images/');
            }
            $thumbFile = $row->getThumbFile() ? 'thumbs/' . $row->getThumbFile() : $row->getSourceFile();
        }

        return '<img style="width: 80px" src="' . $basePath . $thumbFile . '" />';
    }
}