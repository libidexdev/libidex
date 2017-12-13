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
class Magegiant_GiftCard_Block_Adminhtml_Catalog_Product_Composite_Giftcard extends Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options
{
	public function getOptions()
	{
		$_product = $this->getProduct();
		$data     = array();

		$giftCardAmount = $_product->getPriceModel()->analyzeAmount($_product);
		$amount         = array(
			'option_id'  => 'amount',
			'title'      => Mage::helper('giftcard')->__('Amount'),
			'is_require' => true,
			'value'      => $giftCardAmount['amount']
		);
		if ($giftCardAmount['type'] == 'dropdown') {
			$amount['type'] = Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN;
			$amountArray    = array();
			foreach ($giftCardAmount['amount'] as $amt) {
				$amountArray[$amt] = Mage::helper('core')->formatPrice($amt);
			}
			$amount['value'] = $amountArray;
		} elseif ($giftCardAmount['type'] == 'range') {
			$amount['type'] = Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD;
			$amount['note'] = $this->__('Minimum amount: %s. Maximum amount: %s.', '<strong>' . Mage::helper('core')->formatPrice($giftCardAmount['amount']['from']) . '</strong>', '<strong>' . Mage::helper('core')->formatPrice($giftCardAmount['amount']['to']) . '</strong>');
		} else {
			$amount['value']  = Mage::helper('core')->formatPrice($giftCardAmount['amount']);
			$amount['hidden'] = $giftCardAmount['amount'];
		}
		$data[] = $amount;

		$data[] = array(
			'option_id'  => 'sender_name',
			'title'      => Mage::helper('giftcard')->__('Sender Name'),
			'is_require' => true,
			'type'       => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
		);
		if ($this->showEmailField($_product)) {
			$data[] = array(
				'option_id'  => 'sender_email',
				'title'      => Mage::helper('giftcard')->__('Sender Email'),
				'is_require' => true,
				'type'       => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
			);
		}

		$data[] = array(
			'option_id'  => 'recipient_name',
			'title'      => Mage::helper('giftcard')->__('Recipient Name'),
			'type'       => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD,
			'is_require' => true
		);
		if ($this->showEmailField($_product)) {
			$data[] = array(
				'option_id'  => 'recipient_email',
				'title'      => Mage::helper('giftcard')->__('Recipient Email'),
				'type'       => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD,
				'is_require' => true
			);
		}

		if ($this->enableCustomMessage($_product)) {
			$data[] = array(
				'option_id' => 'message',
				'title'     => Mage::helper('giftcard')->__('Message'),
				'type'      => Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA,
				'note'      => $this->__('Maximum number of characters: %s', '<strong>' . $this->getMessageMaxLength() . '</strong>')
			);
		}

		if ($this->enableScheduling($_product)) {
			$data[] = array(
				'option_id' => 'schedule_at',
				'title'     => Mage::helper('giftcard')->__('Send Date'),
				'type'      => Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE
			);
		}

		$options  = array();
		$dataSize = sizeof($data);
		foreach ($data as $option) {
			if (!--$dataSize) {
				$option['is_last'] = true;
			}
			$option['default_value'] = $this->getDefaultValue($option['option_id']);

			$options[] = Mage::getModel('catalog/product_option')->addData($option);
		}

		return $options;
	}

	public function getJsonConfig()
	{
		$_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);

		$product = $this->getProduct();
		$_request->setProductClassId($product->getTaxClassId());
		$defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

		$_request = Mage::getSingleton('tax/calculation')->getRateRequest();
		$_request->setProductClassId($product->getTaxClassId());
		$currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

		$config = array(
			'productId'      => $product->getId(),
			'priceFormat'    => Mage::app()->getLocale()->getJsPriceFormat(),
			'includeTax'     => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
			'showIncludeTax' => Mage::helper('tax')->displayPriceIncludingTax(),
			'showBothPrices' => Mage::helper('tax')->displayBothPrices(),
			'defaultTax'     => $defaultTax,
			'currentTax'     => $currentTax,
		);

		$responseObject = new Varien_Object();
		Mage::dispatchEvent('adminhtml_catalog_product_view_config', array('response_object' => $responseObject));
		if (is_array($responseObject->getAdditionalOptions())) {
			foreach ($responseObject->getAdditionalOptions() as $option => $value) {
				$config[$option] = $value;
			}
		}

		return Mage::helper('core')->jsonEncode($config);
	}

	public function getDefaultValue($code)
	{
		$_product     = $this->getProduct();
		$defaultValue = $_product->getPreconfiguredValues()->getGiftcard();

		if ($defaultValue) {
			return $defaultValue->getData($code);
		}

		return '';
	}

	public function showEmailField($product = null)
	{
		if (!$product) {
			$product = $this->getProduct();
		}

		if (!$product->getTypeInstance()->isPhysical()) {
			return true;
		}

		return false;
	}

	public function enableCustomMessage($product = null)
	{
		if (!$product) {
			$product = $this->getProduct();
		}

		return $this->_helper()->getConfigData($product, 'use_message');
	}

	public function getMessageMaxLength()
	{
		return (int)Mage::helper('giftcard')->getConfig('general/max_message_length');
	}

	public function enableScheduling($product = null)
	{
		if (!$product) {
			$product = $this->getProduct();
		}

		return $this->_helper()->getConfigData($product, 'schedule_enable');
	}

	protected function _helper()
	{
		return Mage::helper('giftcard');
	}
}
