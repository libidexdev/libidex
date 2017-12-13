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
class Magegiant_GiftCardCredit_Model_Adminhtml_Observer
{
	public function orderLoadBlock($observer)
	{
		$request = $observer->getEvent()->getRequest();
		$quote   = Mage::getSingleton('adminhtml/session_quote')->getQuote();

		if ($quote->getCustomerId()) {
			$account = $this->_helper()->getCreditAccount($quote->getCustomerId());
			if ($account && $account->getId() && $account->getBalance()) {
				$isRemove     = $request->getParam('is_remove');
				$creditAmount = Mage::app()->getLocale()->getNumber($request->getParam('credit_amount'));
				if ($creditAmount > $account->getBalance()) {
					$creditAmount = $account->getBalance();
				}

				Mage::getSingleton('adminhtml/session_quote')->setUseCredit(!$isRemove)
					->setGiftcardCreditAmount($creditAmount);
			}
		}

		return $this;
	}

	public function addRefundAction($observer)
	{
		$options = $observer->getEvent()->getOptions();

		$options->setData(Magegiant_GiftCardCredit_Model_Giftcard::REFUND_TO_CREDIT, Mage::helper('giftcardcredit')->__('To Gift Card credit balance'));

		return $this;
	}

	protected function _helper()
	{
		return Mage::helper('giftcardcredit');
	}
}