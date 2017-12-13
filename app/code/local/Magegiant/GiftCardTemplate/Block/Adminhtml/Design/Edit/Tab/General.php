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
class Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
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
        if (Mage::getSingleton('adminhtml/session')->getDesignData()) {
            $data = Mage::getSingleton('adminhtml/session')->getDesignData();
            Mage::getSingleton('adminhtml/session')->setDesignData(null);
        } elseif (Mage::registry('design_data')) {
            $data = Mage::registry('design_data')->getData();
        }
        $fieldset = $form->addFieldset('design_general', array(
            'legend' => $helper->__('Design information')
        ));

        $fieldset->addField('name', 'text', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Name'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'name',
        ));
        $fieldset->addField(
            'website_ids',
            'multiselect',
            array(
                'name'               => 'website_ids',
                'title'              => $helper->__('Website'),
                'label'              => $helper->__('Website'),
                'values'             => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(),
                'required'           => true,
                'after_element_html' => $helper->addSelectAll('website_ids'),
            )
        );
        $groups = Mage::getResourceModel('customer/group_collection')
            ->load()
            ->toOptionArray();

        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            array(
                'name'               => 'customer_group_ids',
                'title'              => $helper->__('Customer Group'),
                'label'              => $helper->__('Customer Group'),
                'values'             => $groups,
                'required'           => true,
                'after_element_html' => $helper->addSelectAll('customer_group_ids'),
            )
        );
        $fieldset->addField('status', 'select', array(
            'label'  => Mage::helper('giftcardtemplate')->__('Status'),
            'name'   => 'status',
            'values' => Mage::getSingleton('giftcardtemplate/status')->getOptionHash(),
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label'    => Mage::helper('giftcardtemplate')->__('Sort Order'),
            'required' => false,
            'name'     => 'sort_order',
        ));
        $fieldset->addField(
            'item_ids',
            'hidden',
            array(
                'name'  => 'item_ids',
            )
        );
        $data['item_ids'] = $this->_getSelectedItems();
        $form->setValues($data);

        return parent::_prepareForm();
    }

    protected function _getSelectedItems()
    {
        $ids      = array();
        $designId = $this->getRequest()->getParam('id');
        if ($designId) {
            $details = Mage::getModel('giftcardtemplate/design_items_detail')->getCollection()
                ->addFieldToFilter('design_id', $designId);
            foreach ($details as $detail) {
                $ids[] = $detail->getItemId();
            }
        }

        return implode(',', $ids);
    }
}