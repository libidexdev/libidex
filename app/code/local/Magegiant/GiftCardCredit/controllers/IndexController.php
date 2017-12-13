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
class Magegiant_GiftCardCredit_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function redeemAction()
	{
		if(!$this->_getSession()->isLoggedIn()){
			$this->_goBack();

			return;
		}

		$giftCode = (string)$this->getRequest()->getParam('gift_code');
		if (!strlen($giftCode)) {
			$this->_goBack();

			return;
		}

		try {
			$account = Mage::getModel('giftcardcredit/account')->load($this->_getSession()->getCustomerId(), 'customer_id');
			$account->redeem($giftCode);

			$this->_getSession()->addSuccess(
				$this->__('Gift card code "%s" was redeemed successfully.', Mage::helper('core')->escapeHtml($giftCode))
			);
		} catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_goBack();

		return;
	}

	protected function _goBack()
	{
		$this->_redirect('giftcard');

		return $this;
	}

	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}
}