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

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'Order' . DS . 'CreateController.php';

class Magegiant_GiftCard_Adminhtml_Giftcard_OrderController extends Mage_Adminhtml_Sales_Order_CreateController
{
	public function loadBlockAction()
	{
		$giftCode = (string)$this->getRequest()->getParam('gift_code');
		if (strlen($giftCode)) {
			$giftCodeList = $this->_getSession()->getGiftCodes();
			if (!is_array($giftCodeList)) {
				$giftCodeList = array();
			}

			if ($this->getRequest()->getParam('is_remove')) {
				$key = array_search($giftCode, $giftCodeList);
				if ($key !== false) {
					unset($giftCodeList[$key]);
				}
			} elseif (!in_array($giftCode, $giftCodeList)) {
				$giftCodeList[] = $giftCode;
			}

			$this->_getSession()->setGiftCodes($giftCodeList);
		}

		Mage::dispatchEvent('giftcard_adminhtml_sales_order_load_block', array(
			'request' => $this->getRequest(),
			'action'  => $this
		));

		parent::loadBlockAction();
	}
}
