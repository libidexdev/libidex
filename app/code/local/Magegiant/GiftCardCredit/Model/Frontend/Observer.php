<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * GiftCardCredit Observer Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardCredit_Model_Frontend_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Magegiant_GiftCardCredit_Model_Observer
	 */
	public function addLinkRedeemToList($observer)
	{
		$card    = $observer->getEvent()->getcard();
		$actions = $observer->getEvent()->getAction();

		$giftCard = Mage::getModel('giftcard/giftcard')->load($card->getGiftcardId());

		if ($this->_helper()->allowRedeem($giftCard)) {
			$actions->setData('redeem', array(
				'label'  => $this->_helper()->__('Redeem'),
				'action' => Mage::getUrl('giftcardcredit/index/redeem', array('gift_code' => $giftCard->getCode()))
			));
		}

		return $this;
	}

	public function addGiftCardCreditForm($observer)
	{
		$helper = Mage::helper('giftcardcredit');
		if (!$helper->isEnabled() || !$helper->isModuleOutputEnabled('Magegiant_GiftCardCredit')) {
			return $this;
		}

		$block = $observer->getEvent()->getBlock();
		if ($block instanceof Mage_Checkout_Block_Cart_Coupon && $helper->allowGiftcardCreditBox()) {
			$transport = $observer->getEvent()->getTransport();
			$html      = $transport->getHtml();

			$html .= $block->getLayout()->createBlock('giftcardcredit/checkout_cart_form')->toHtml();

			$transport->setHtml($html);
		}

		if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods && $helper->allowGiftcardCreditBox()) {
			$transport = $observer->getEvent()->getTransport();
			$html      = $transport->getHtml();

			$gcHtml = $block->getLayout()->createBlock('giftcardcredit/checkout_onepage_form')->toHtml();

			$html .= '<script type="text/javascript">
				var container = ' . $helper->jsonEncode(array('html' => $gcHtml)) . ';
				if ($("checkout-payment-method-load").down("#checkout-payment-method-load") == undefined) {
					var gcCreditPaymentElement = $("checkout-payment-method-load");
				} else {
					var gcCreditPaymentElement = $("checkout-payment-method-load").down("#checkout-payment-method-load");
				}

				gcCreditPaymentElement.insert({
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

	protected function _helper()
	{
		return Mage::helper('giftcardcredit');
	}
}