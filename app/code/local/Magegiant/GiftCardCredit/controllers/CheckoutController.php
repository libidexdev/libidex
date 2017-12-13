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
 * @package     Magegiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * GiftCardCredit Index Controller
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardCredit
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardCredit_CheckoutController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function creditPostAction()
	{
		$quote = $this->_getQuote();
		if (!$quote->getItemsCount()) {
			$this->_goBack();

			return;
		}

		$useCredit = $this->getRequest()->getParam('use_credit', true);
		$creditAmount = Mage::app()->getLocale()->getNumber($this->getRequest()->getParam('credit_amount'));
		if ($this->getRequest()->getParam('cancel') == 1) {
			$creditAmount = 0;
		}

		$oldCreditAmount = $this->_getSession()->getGiftcardCreditAmount();
		if (!$creditAmount && !$oldCreditAmount) {
			$this->_goBack();

			return;
		}

		try {
			Mage::helper('giftcardcredit')->applyCreditForQuote($creditAmount, $useCredit);

			if ($creditAmount) {
				if ($quote->getGiftcardCreditAmount()) {
					$this->_getSession()->addSuccess(
						$this->__('Credit amount %s was applied for this order.', Mage::helper('core')->formatPrice(abs($quote->getGiftcardCreditAmount())))
					);
				} else {
					$this->_getSession()->addError(
						$this->__('Gift Card credit cannot apply for this order.')
					);
				}
			} else {
				$this->_getSession()->addSuccess($this->__('Credit was canceled.'));
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_goBack();

		return;
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
}