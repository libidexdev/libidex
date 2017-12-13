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

class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Actions
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
		return Mage::helper('giftcard')->__('Cart Item Conditions');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('giftcard')->__('Cart Item Conditions');
	}

	/**
	 * Returns status flag about this tab can be showen or not
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

	/**
	 * @return \Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm()
	{
		$giftcard = Mage::registry('current_giftcard');
		$form     = new Varien_Data_Form();
		$form->setHtmlIdPrefix('rule_');

		/** Action */
		$renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
			->setTemplate('promo/fieldset.phtml')
			->setNewChildUrl($this->getUrl('adminhtml/giftcard_product/newActionHtml/form/rule_actions_fieldset'));

		$actionFieldset = $form->addFieldset('actions_fieldset', array(
			'legend' => Mage::helper('salesrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)')
		))->setRenderer($renderer);

		$actionFieldset->addField('actions', 'text', array(
			'name'     => 'actions',
			'label'    => Mage::helper('salesrule')->__('Apply To'),
			'title'    => Mage::helper('salesrule')->__('Apply To'),
			'required' => true,
		))->setRule($giftcard)->setRenderer(Mage::getBlockSingleton('rule/actions'));

		$this->setForm($form);

		Mage::dispatchEvent('magegiant_giftcard_edit_tab_actions', array(
			'form' => $form
		));

		return parent::_prepareForm();
	}
}
