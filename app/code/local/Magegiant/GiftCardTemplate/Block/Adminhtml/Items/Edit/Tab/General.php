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
 * Giftcardtemplate Edit Form Content Tab Block
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Items_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $helper = Mage::helper('giftcardtemplate');
        if (Mage::getSingleton('adminhtml/session')->getItemData()) {
            $data = Mage::getSingleton('adminhtml/session')->getItemData();
            Mage::getSingleton('adminhtml/session')->setItemData(null);
        } elseif (Mage::registry('item_data')) {
            $data = Mage::registry('item_data')->getData();
        }
        $fieldset = $form->addFieldset('item_general', array(
            'legend' => $helper->__('Design Information')
        ));

        $fieldset->addField('name', 'text', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Name'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'name',
        ));
        $fieldset->addField('format_id', 'select', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Format'),
            'title'    => $helper->__('Format'),
            'name'     => 'format_id',
            'values'   => Mage::getSingleton('giftcardtemplate/format_options')->getOptionHash(),
            'onchange' => 'changeFormatId()'
        ));
        $fieldset->addField('source_file', 'image', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Source File'),
            'title'    => $helper->__('Format'),
            'name'     => 'source_file',
            'required' => true
        ));
        $fieldset->addField('video_url', 'text', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Video Url'),
            'title'    => $helper->__('Format'),
            'name'     => 'video_url',
            'required' => true
        ));
        $fieldset->addField('thumb_file', 'image', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Thumb File'),
            'title'    => $helper->__('Format'),
            'name'     => 'thumb_file',
            'required' => false
        ));
        $fieldset->addField('status', 'select', array(
            'label'  => Mage::helper('giftcardtemplate')->__('Status'),
            'name'   => 'status',
            'values' => Mage::getSingleton('giftcardtemplate/status')->getOptionHash(),
        ));
        if (isset($data['is_default']) && $data['is_default']) {
            $basePath = 'https://raw.githubusercontent.com/magegiant/giftcardtemplate/master/media/magegiant/giftcardtemplate/images/';
        } else {
            $basePath = Mage::helper('giftcardtemplate/upload')->getBaseImageUrl('images/');
        }
        if (isset($data['source_file']) && !empty($data['source_file'])) {
            $data['source_file'] = $basePath . $data['source_file'];
        }
        if (isset($data['thumb_file']) && !empty($data['thumb_file'])) {

            $data['thumb_file'] = $basePath . 'thumbs/' . $data['thumb_file'];
        }
        $form->setValues($data);

        return parent::_prepareForm();
    }
}