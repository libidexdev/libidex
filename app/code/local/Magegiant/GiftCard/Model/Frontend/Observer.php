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
class Magegiant_GiftCard_Model_Frontend_Observer
{
	public function addGiftCardForm($observer)
	{
		$helper = Mage::helper('giftcard');
		if (!$helper->isEnabled() || !$helper->isModuleOutputEnabled('Magegiant_GiftCard')) {
			return $this;
		}

		$block = $observer->getEvent()->getBlock();
		if ($block instanceof Mage_Checkout_Block_Cart_Coupon && $helper->allowGiftcardBox()) {
			if (!$helper->getConfig('redeem/display/box')) {
				return $this;
			}

			$transport = $observer->getEvent()->getTransport();
			$html      = $transport->getHtml();

			$html .= $block->getLayout()->createBlock('giftcard/checkout_cart_form')->toHtml();

			$transport->setHtml($html);
		}

		if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods && $helper->allowGiftcardBox()) {
			if (!$helper->getConfig('redeem/display/payment_section')) {
				return $this;
			}
			$transport = $observer->getEvent()->getTransport();
			$html      = $transport->getHtml();

			$gcHtml = $block->getLayout()->createBlock('giftcard/checkout_onepage_form')->toHtml();

			$html .= '<script type="text/javascript">
				var container = ' . $helper->jsonEncode(array('html' => $gcHtml)) . ';
				if ($("checkout-payment-method-load").down("#checkout-payment-method-load") == undefined) {
					var gcPaymentElement = $("checkout-payment-method-load");
				} else {
					var gcPaymentElement = $("checkout-payment-method-load").down("#checkout-payment-method-load");
				}

				gcPaymentElement.insert({
					top: container.html
				});
				if ($("p_method_free")) {
					$("p_method_free").checked = true;
				}
    		</script>';

			$transport->setHtml($html);
		}

		return $this;
	}

	public function couponPost($observer)
	{
		$helper = Mage::helper('giftcard');
		if (!$helper->isEnabled() || $helper->getConfig('redeem/display/box')) {
			return $this;
		}

		if (!$this->_getSession()->getQuote()->getItemsCount()) {
			return $this;
		}

		$action   = $observer->getEvent()->getControllerAction();
		$giftCode = (string)$action->getRequest()->getParam('coupon_code');
		if (!strlen($giftCode)) {
			return $this;
		}

		try {
			if (Mage::helper('giftcard')->addGiftCode($giftCode)) {
				$this->_getSession()->addSuccess(
					Mage::helper('giftcard')->__('Gift card code "%s" was applied.', Mage::helper('core')->escapeHtml($giftCode))
				);
			}

			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
		} catch (Exception $e) {

		}

		return $this;
	}

	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
}