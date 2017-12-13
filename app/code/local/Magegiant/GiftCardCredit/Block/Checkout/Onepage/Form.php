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
class Magegiant_GiftCardCredit_Block_Checkout_Onepage_Form
	extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('magegiant/giftcardcredit/checkout/onepage/form.phtml');
	}

	public function useCredit()
	{
		return Mage::getSingleton('checkout/session')->getUseCredit();
	}

	public function getCreditUsed()
	{
		return Mage::getSingleton('checkout/session')->getGiftcardCreditAmount();
	}

	public function getMessage()
	{
		$message = Mage::getSingleton('checkout/session')->getMessages(true);
		if ($message) {
			$block = Mage::getBlockSingleton('core/messages')->addMessages($message);

			return $block->toHtml();
		}

		return '';
	}

	public function getCreditUrl($isCancel = false){
		if($isCancel){
			return $this->getUrl('giftcardcredit/checkout/creditPost', array('ajax' => true, 'cancel' => true));
		}

		return $this->getUrl('giftcardcredit/checkout/creditPost', array('ajax' => true));
	}
}
