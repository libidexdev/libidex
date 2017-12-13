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
class Magegiant_GiftCard_Model_Mysql4_Giftcard extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('giftcard/giftcard', 'giftcard_id');
	}

	public function checkCode($code)
	{
		$read   = $this->_getReadAdapter();
		$select = $read->select();
		$select->from($this->getMainTable(), $this->getIdFieldName());
		$select->where('code = :code');

		if ($read->fetchOne($select, array('code' => $code)) === false) {
			return false;
		}

		return true;
	}

	public function updateStatus($ids, $status)
	{
		$where[$this->getIdFieldName() . ' IN (?)'] = $ids;

		$this->_getWriteAdapter()->update($this->getMainTable(), array('status' => $status), $where);

		return $this;
	}

	public function createMultiple($giftCard)
	{
		$data  = array();
		$codes = array();

		if ($giftCard instanceof Magegiant_GiftCard_Model_Giftcard) {
			$model = Mage::getModel('giftcard/giftcard');
			$qty   = $giftCard->getQty();
			while ($qty--) {
				$code    = $model->generateCode($giftCard->getPattern());
				$codes[] = $code;

				$data[] = array(
					'code'                  => $code,
					'name'                  => $giftCard->getName(),
					'created_at'            => $this->formatDate($giftCard->getCreatedAt()),
					'expired_at'            => $this->formatDate($giftCard->getExpiredAt()),
					'amount'                => $giftCard->getAmount(),
					'conditions_serialized' => $giftCard->getConditionsSerialized(),
					'actions_serialized'    => $giftCard->getActionsSerialized(),
					'status'                => Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE,
					'active'                => $giftCard->getActive(),
					'website_id'            => $giftCard->getWebsiteId(),
					'store_id'              => $giftCard->getStoreId(),
					'sender_name'           => $giftCard->getSenderName(),
					'sender_email'          => $giftCard->getSenderEmail(),
					'recipient_name'        => $giftCard->getRecipientName(),
					'recipient_email'       => $giftCard->getRecipientEmail(),
					'message'               => $giftCard->getMessage()
				);
			}
		} else if (is_array($giftCard)) {
			$data  = $giftCard;
			$codes = array_column($giftCard, 'code');
		}

		if(!empty($data)) {
			$this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);
		}

		return $codes;
	}
}