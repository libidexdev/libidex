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

class Magegiant_GiftCard_Model_Mysql4_History extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('giftcard/history', 'history_id');
	}

	public function updateStatus($collection, $status, $detail)
	{
		$data = array();
		foreach ($collection as $giftCard) {
			$message = $detail . ' ' . Mage::helper('giftcard')->__('Status Change: %s', $giftCard->getStatusLabel($status));
			$data[]  = array(
				'action'         => Magegiant_GiftCard_Model_History::ACTION_UPDATED,
				'giftcard_id'    => $giftCard->getId(),
				'giftcard_code'  => $giftCard->getCode(),
				'amount'         => $giftCard->getAmount(),
				'amount_change'  => 0,
				'updated_at'     => Varien_Date::now(),
				'history_detail' => $message
			);
		}

		$this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);

		return $this;
	}

	public function createMultiple($giftCards, $detail = null){
		$data = array();
		foreach($giftCards as $card){
			$data[]  = array(
				'action'         => Magegiant_GiftCard_Model_History::ACTION_CREATED,
				'giftcard_id'    => $card->getId(),
				'giftcard_code'  => $card->getCode(),
				'amount'         => $card->getAmount(),
				'amount_change'  => 0,
				'updated_at'     => Varien_Date::now(),
				'history_detail' => $detail ? $detail : Mage::helper('giftcard')->__('Created by: %s.', Mage::getSingleton('admin/session')->getUser()->getUsername())
			);
		}

		$this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);

		return $this;
	}
}