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
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */
class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Send
	extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * Prepare content for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return Mage::helper('giftcard')->__('Send Gift Card');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('giftcard')->__('Send Gift Card');
	}

	/**
	 * Returns status flag about this tab can be showed or not
	 *
	 * @return true
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Returns status flag about this tab hidden or not
	 *
	 * @return true
	 */
	public function isHidden()
	{
		return false;
	}

	protected function _prepareForm()
	{
		$campaign = Mage::registry('current_giftcard');

		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('customer_fieldset', array(
			'legend' => Mage::helper('giftcard')->__('Sender Information')
		));

		$fieldset->addField('sender_name', 'text', array(
			'name'  => 'sender_name',
			'label' => Mage::helper('giftcard')->__('Name'),
			'title' => Mage::helper('giftcard')->__('Name')
		));

		$fieldset->addField('sender_email', 'text', array(
			'name'  => 'sender_email',
			'label' => Mage::helper('giftcard')->__('Email'),
			'title' => Mage::helper('giftcard')->__('Email'),
			'class' => 'validate-email'
		));

		$sendFieldset = $form->addFieldset('send_fieldset', array(
			'legend' => Mage::helper('giftcard')->__('Recipient Information')
		));

		$sendFieldset->addField('recipient_name', 'text', array(
			'name'  => 'recipient_name',
			'label' => Mage::helper('giftcard')->__('Name'),
			'title' => Mage::helper('giftcard')->__('Name')
		));

		$sendFieldset->addField('recipient_email', 'text', array(
			'name'  => 'recipient_email',
			'label' => Mage::helper('giftcard')->__('Email'),
			'title' => Mage::helper('giftcard')->__('Email'),
			'class' => 'validate-email'
		));

		$sendFieldset->addField('message', 'textarea', array(
			'name'  => 'message',
			'label' => Mage::helper('giftcard')->__('Message'),
			'title' => Mage::helper('giftcard')->__('Message')
		));

		$store = $sendFieldset->addField('store_id', 'select', array(
			'name'     => 'store_id',
			'label'    => Mage::helper('giftcard')->__('Send Email from Store'),
			'title'    => Mage::helper('giftcard')->__('Send Email from Store'),
			'renderer' => Mage::app()->getLayout()->createBlock('adminhtml/system_config_switcher')
		));
		$store->setRenderer(Mage::app()->getLayout()->createBlock('giftcard/adminhtml_giftcard_edit_tab_renderer_store'));

		Mage::dispatchEvent('magegiant_giftcard_edit_tab_send', array(
			'form' => $form
		));

		$form->setValues($campaign->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

}
