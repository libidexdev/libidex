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

class Magegiant_GiftCard_Model_Adminhtml_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Magegiant_GiftCard_Model_Observer
	 */
	public function setAttributesRendererInForm($observer)
	{
		$form = $observer->getEvent()->getForm();

		$giftCardElement = $form->getElement('giftcard_amount_type');
		if ($giftCardElement) {
			/**
			 * Add amount renderer
			 */
			$element = $form->getElement('giftcard_amount');
			if ($element) {
				$element->setRenderer(
					Mage::app()->getLayout()->createBlock('giftcard/adminhtml_catalog_product_renderer_amount')->setEntityAttribute($element->getEntityAttribute())
				);
			}

			$fieldSet = $this->getFieldSet($form);
			if ($fieldSet) {
				/**
				 * Add Dependence
				 */
				$giftCardAmounts = $form->getElement('giftcard_amount');
				$rangeMax        = $form->getElement('giftcard_amount_from');
				$rangeMin        = $form->getElement('giftcard_amount_to');
				$pricePercent    = $form->getElement('giftcard_price_percent');

				$dependenceBlock = Mage::app()->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
					->addFieldMap($giftCardElement->getHtmlId(), $giftCardElement->getName())
					->addFieldMap($giftCardAmounts->getHtmlId(), $giftCardAmounts->getName())
					->addFieldMap($rangeMax->getHtmlId(), $rangeMax->getName())
					->addFieldMap($rangeMin->getHtmlId(), $rangeMin->getName())
					->addFieldMap($pricePercent->getHtmlId(), $pricePercent->getName())
					->addFieldDependence($giftCardAmounts->getName(), $giftCardElement->getName(), '1')
					->addFieldDependence($rangeMax->getName(), $giftCardElement->getName(), '0')
					->addFieldDependence($rangeMin->getName(), $giftCardElement->getName(), '0')
					->addFieldDependence($pricePercent->getName(), $giftCardElement->getName(), '0');

				$fieldSet->addField('giftcard_dependece', 'note', array(
					'text' => $dependenceBlock->toHtml()
				));
				$fieldSet->addField('giftcard_type_js', 'note', array(
					'text' => "<script type=\"text/javascript\">
								//<![CDATA[
								$('giftcard_amount_type').up('div.fieldset').removeClassName('fieldset-wide');
								//]]>
							</script>"
				));
			}
		}

		$giftCardProductElement = $form->getElement('giftcard_product_type');
		if ($giftCardProductElement) {
			/**
			 * Prepare conditions, action
			 */
			Mage::app()->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
			$product = $form->getDataObject();
			$model   = Mage::getModel('giftcard/giftcard');
			$model->setConditions(null)
				->setActions(null)
				->setData('conditions_serialized', $product->getData('giftcard_conditions_serialized'))
				->setData('actions_serialized', $product->getData('giftcard_actions_serialized'));

			Mage::dispatchEvent('adminhtml_giftcard_information_add_fieldset_before', array('form' => $form));

			$this->addConditionFieldset($form, $model);
			$this->addActionFieldset($form, $model);

			Mage::dispatchEvent('adminhtml_giftcard_information_add_fieldset_after', array('form' => $form));

			$fieldSet = $this->getFieldSet($form);
			if ($fieldSet) {
				/**
				 * Add javascript
				 */
				$fieldSet->addField('giftcard_type_js', 'note', array(
					'text' => "<script type=\"text/javascript\">
								//<![CDATA[
								function togglecardType() {
									if ($('weight') != undefined) {
										var weight = $('weight');
										var span = weight.up('tr').down('td').down('label > span');
										if ($('giftcard_product_type').value == '0') {
											while(weight.hasClassName('required-entry')) {
												weight.removeClassName('required-entry');
											}
											if (span != undefined) {
												span.hide();
											}
										} else {
											weight.addClassName('required-entry');
											if (span != undefined) {
												span.show();
											}
										}
									}
									var isVirtual = parseInt($('giftcard_product_type').value) == 0;

									$$('.hidden-for-virtual').each(function(el){
										if (isVirtual) {
											el.up('tr').hide();
										} else {
											el.up('tr').show();
										}
									})
								}

								function giftcardToggleDefault(v) {
									defaultValue = $('giftcard_' + v + '_default');
									checkbox = $('giftcard_use_config_' + v);
									if (checkbox.checked) {
										$('giftcard_' + v).value = defaultValue.value;
									}
								}

								togglecardType();
								Event.observe('giftcard_product_type', 'change', togglecardType);

								$('giftcard_product_type').up('div.fieldset').removeClassName('fieldset-wide');
								//]]>
							</script>
							<style type='text/css'>
							#conditions_description.textarea{height: 4em !important;}
							</style>"
				));
			}
		}

		return $this;
	}

	public function getFieldSet($form)
	{
		foreach ($form->getElements() as $element) {
			if ($element->getType() == 'fieldset') {
				return $element;
			}
		}

		return null;
	}

	public function addConditionFieldset($form, $model)
	{
		$renderer = Mage::app()->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
			->setTemplate('promo/fieldset.phtml')
			->setNewChildUrl(Mage::getUrl('adminhtml/giftcard_product/newConditionHtml/form/conditions_fieldset'));
		$fieldset = $form->addFieldset('conditions_fieldset', array('legend' => Mage::helper('giftcard')->__('Allow using Gift Card only if the following shopping cart conditions are met (leave blank for all shopping carts)')))->setRenderer($renderer);

		$fieldset->addField('conditions', 'text', array(
			'name'     => 'conditions',
			'label'    => Mage::helper('giftcard')->__('Conditions'),
			'title'    => Mage::helper('giftcard')->__('Conditions'),
			'required' => true,
		))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

		return $fieldset;
	}

	public function addActionFieldset($form, $model)
	{
		$renderer = Mage::app()->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
			->setTemplate('promo/fieldset.phtml')
			->setNewChildUrl(Mage::getUrl('adminhtml/giftcard_product/newActionHtml/form/actions_fieldset'));

		$fieldset = $form->addFieldset('actions_fieldset', array(
			'legend' => Mage::helper('salesrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)')
		))->setRenderer($renderer);

		$fieldset->addField('actions', 'text', array(
			'name'     => 'actions',
			'label'    => Mage::helper('salesrule')->__('Actions'),
			'title'    => Mage::helper('salesrule')->__('Actions'),
			'required' => true,
		))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

		return $fieldset;
	}

	public function productPrepareSave($observer)
	{
		$product = $observer->getEvent()->getProduct();
		if ($product->getTypeId() != Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE) {
			return $this;
		}

		$data = $observer->getEvent()->getRequest()->getParam('rule');

		$model = Mage::getModel('giftcard/giftcard');
		$model->loadPost($data);

		if (isset($data['conditions'])) {
			$product->setData('giftcard_conditions_serialized', serialize($model->getConditions()->asArray()));
		}
		if (isset($data['actions'])) {
			$product->setData('giftcard_actions_serialized', serialize($model->getActions()->asArray()));
		}
		if ($amount = $product->getData('giftcard_amount')) {
			$product->setData('giftcard_amount', serialize($amount));
		}

		return $this;
	}
}