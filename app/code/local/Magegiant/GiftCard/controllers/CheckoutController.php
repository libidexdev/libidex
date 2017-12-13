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

class Magegiant_GiftCard_CheckoutController extends Mage_Core_Controller_Front_Action
{
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}

	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}

	protected function _getQuote()
	{
		return $this->_getCart()->getQuote();
	}

	protected function _goBack()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$result = array(
				'update_payment' => true
			);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		} else {
			$this->_redirect('checkout/cart');
		}

		return $this;
	}

	public function addCodeAction()
	{
		if (!$this->_getQuote()->getItemsCount()) {
			$this->_goBack();

			return;
		}

		$giftCode = (string)$this->getRequest()->getParam('gift_code');
		if (!strlen($giftCode)) {
			$this->_goBack();

			return;
		}

		try {
			if(Mage::helper('giftcard')->addGiftCode($giftCode)) {
				$this->_getSession()->addSuccess(
					$this->__('Gift card code "%s" was applied.', Mage::helper('core')->escapeHtml($giftCode))
				);
			}
		} catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_goBack();

		return;
	}

	public function removeCodeAction()
	{
		$giftCode     = $this->getRequest()->getParam('gift_code');
		$giftCodeList = $this->_getSession()->getGiftCodes();
		if (!is_array($giftCodeList)) {
			$giftCodeList = array();
		}
		$key = array_search($giftCode, $giftCodeList);
		if ($key !== false) {
			unset($giftCodeList[$key]);
		}
		$this->_getSession()->setGiftCodes($giftCodeList);

		$this->_getSession()->addSuccess(
			$this->__('Gift card code "%s" was removed.', Mage::helper('core')->escapeHtml($giftCode))
		);

		$this->_goBack();

		return;
	}

	public function checkPostAction()
	{
		if (!Mage::helper('giftcard')->getConfig('redeem/display/check_code')) {
			return;
		}

		$block = Mage::getBlockSingleton('giftcard/checkout_cart_check');

		$giftCode = (string)$this->getRequest()->getParam('gift_code');
		if (!strlen($giftCode)) {
			$html = $block->setValid(false)
				->setMessage($this->__('Gift Card Code is invalid.'))
				->toHtml();
		} else {
			$giftCard = Mage::getModel('giftcard/giftcard')->load($giftCode, 'code');
			if (!$giftCard->isActive(true, true, true, true)) {
				$html = $block->setValid(false)
					->setMessage($this->__('Gift Card Code is invalid.'))
					->toHtml();
			} else {
				$html = $block->setValid(true)
					->setGiftCard($giftCard)
					->toHtml();
			}
		}

		$result = array(
			'html' => $html
		);

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

		return;
	}
}