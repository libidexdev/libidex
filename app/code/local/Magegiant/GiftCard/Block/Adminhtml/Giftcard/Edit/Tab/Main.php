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
class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Main
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
		return Mage::helper('giftcard')->__('Gift Card Information');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('giftcard')->__('Gift Card Information');
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
		$model = Mage::registry('current_giftcard');

		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('campaign_form', array(
			'legend' => Mage::helper('giftcard')->__('General Information')
		));

		if ($this->isCreate()) {
			$fieldset->addField('pattern', 'text', array(
				'name'               => 'pattern',
				'label'              => Mage::helper('giftcard')->__('Gift Code Pattern'),
				'title'              => Mage::helper('giftcard')->__('Gift Code Pattern'),
				'after_element_html' => '<div class="field-tooltip"><div><strong>{8L} : 8 Leter<br>{4D} : 4 Digit<br>{6LD} : 6 Letter-Digit<br>GIFT-{4L}-{6LD} : GIFT-UEKC-CG45J8</strong></div></div><style type="text/css">.hor-scroll{overflow: visible !important;}</style>',
			));

			$fieldset->addField('qty', 'text', array(
				'name'  => 'qty',
				'label' => Mage::helper('giftcard')->__('Gift Code Qty'),
				'title' => Mage::helper('giftcard')->__('Gift Code Qty'),
				'note'  => Mage::helper('giftcard')->__('The number of codes will be generated. Email cannot be sent if a number of gift codes greater than 10.')
			));

			$model->addData(array(
				'pattern' => $model->getPattern() ? $model->getPattern() : Mage::helper('giftcard')->getConfig('gift_code/pattern'),
				'active'  => $model->getActive() ? $model->getActive() : Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE,
				'qty'     => $model->getQty() ? $model->getQty() : 1
			));
		} else {
			$fieldset->addField('code', 'label', array(
				'name'  => 'code',
				'label' => Mage::helper('giftcard')->__('Gift Card Code'),
				'title' => Mage::helper('giftcard')->__('Gift Card Code')
			));

			$fieldset->addField('status_label', 'label', array(
				'name'  => 'status_label',
				'label' => Mage::helper('giftcard')->__('Status'),
				'title' => Mage::helper('giftcard')->__('Status')
			));
		}

		$fieldset->addField('name', 'text', array(
			'name'  => 'name',
			'label' => Mage::helper('giftcard')->__('Gift Card Name'),
			'title' => Mage::helper('giftcard')->__('Gift Card Name'),
			'note'  => Mage::helper('giftcard')->__('Optional. This field will help you separate gift card type.')
		));

		$fieldset->addField('active', 'select', array(
			'label'    => Mage::helper('giftcard')->__('Active'),
			'title'    => Mage::helper('giftcard')->__('Active'),
			'name'     => 'active',
			'required' => true,
			'options'  => array(
				Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE   => Mage::helper('giftcard')->__('Yes'),
				Magegiant_GiftCard_Model_Giftcard::STATE_INACTIVE => Mage::helper('giftcard')->__('No'),
			)
		));

		$fieldset->addField('amount', 'text', array(
			'label'              => Mage::helper('giftcard')->__('Amount'),
			'title'              => Mage::helper('giftcard')->__('Amount'),
			'name'               => 'amount',
			'class'              => 'validate-number validate-zero-or-greater',
			'required'           => true,
			'after_element_html' => '<strong>[' . Mage::app()->getBaseCurrencyCode() . ']</strong>'
		));

		$fieldset->addField('website_id', 'select', array(
			'name'     => 'website_id',
			'label'    => Mage::helper('giftcard')->__('Website'),
			'title'    => Mage::helper('giftcard')->__('Website'),
			'required' => true,
			'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(),
		));

		$fieldset->addField('expired_at', 'date', array(
			'name'   => 'expired_at',
			'label'  => Mage::helper('giftcard')->__('Expired on'),
			'title'  => Mage::helper('giftcard')->__('Expired on'),
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
		));

		Mage::dispatchEvent('magegiant_giftcard_edit_tab_main', array(
			'form' => $form
		));

		$form->setValues($model->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	public function isCreate()
	{
		$giftcard = Mage::registry('current_giftcard');

		if ($giftcard && $giftcard->getId()) {
			return false;
		}

		return true;
	}
}
