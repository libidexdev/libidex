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

class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Conditions
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
		return Mage::helper('giftcard')->__('Shopping Cart Conditions');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('giftcard')->__('Shopping Cart Conditions');
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

	protected function _prepareForm()
	{
		$giftcard = Mage::registry('current_giftcard');

		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('rule_');

		$renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
			->setTemplate('promo/fieldset.phtml')
			->setNewChildUrl($this->getUrl('adminhtml/giftcard_product/newConditionHtml/form/rule_conditions_fieldset'));

		$fieldset = $form->addFieldset('conditions_fieldset', array(
			'legend' => Mage::helper('giftcard')->__('Apply the rule only if the following conditions are met (leave blank for all products)'),
		))
			->setRenderer($renderer);

		$fieldset->addField('conditions', 'text', array(
			'name'     => 'conditions',
			'label'    => Mage::helper('giftcard')->__('Conditions'),
			'title'    => Mage::helper('giftcard')->__('Conditions'),
			'required' => true,
		))
			->setRule($giftcard)
			->setRenderer(Mage::getBlockSingleton('rule/conditions'));

		$this->setForm($form);

		Mage::dispatchEvent('magegiant_giftcard_edit_tab_conditions', array(
			'form' => $form
		));

		return parent::_prepareForm();
	}
}