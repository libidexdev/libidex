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

class Magegiant_GiftCard_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
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
		Mage::dispatchEvent('catalog_product_view_config', array('response_object' => $responseObject));
		if (is_array($responseObject->getAdditionalOptions())) {
			foreach ($responseObject->getAdditionalOptions() as $option => $value) {
				$config[$option] = $value;
			}
		}

		return Mage::helper('core')->jsonEncode($config);
	}

	public function showEmailField($product)
	{
		if (!$product->getTypeInstance()->isPhysical()) {
			return true;
		}

		return false;
	}

	public function enableCustomMessage($product)
	{
		return $this->_helper()->getConfigData($product, 'use_message');
	}

	public function getMessageMaxLength($product)
	{
		return (int)Mage::helper('giftcard')->getConfig('general/max_message_length');
	}

	public function enableScheduling($product)
	{
		return $this->_helper()->getConfigData($product, 'schedule_enable');
	}

	public function getFormData()
	{
		$product = $this->getProduct();
		if ($product->getConfigureMode()) {
			return $product->getPreconfiguredValues()->getGiftcard();
		}

		return new Varien_Object();
	}

	public function getJsPriceFormat()
	{
		return Mage::helper('core')->jsonEncode(Mage::app()->getLocale()->getJsPriceFormat());
	}

	protected function _helper()
	{
		return Mage::helper('giftcard');
	}

	public function displayProductStockStatus()
	{
		$statusInfo = new Varien_Object(array('display_status' => true));
		Mage::dispatchEvent('catalog_block_product_status_display', array('status' => $statusInfo));
		return (boolean)$statusInfo->getDisplayStatus();
	}
}
