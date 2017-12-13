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

class Magegiant_GiftCard_IndexController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();

		if (!$this->getRequest()->isDispatched()) {
			return;
		}
		if (!Mage::helper('giftcard')->isEnabled()) {
			$this->_forward('noRoute');
			$this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

			return;
		}

		if (!$this->_getSession()->authenticate($this)) {
			$this->_getSession()->setAfterAuthUrl(
				Mage::getUrl($this->getFullActionName('/'))
			);
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	/**
	 * index action
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_title($this->__('Gift Card'));
		$this->renderLayout();
	}

	public function addAction()
	{
		$success  = false;
		$giftCode = (string)$this->getRequest()->getParam('gift_code');
		if (strlen($giftCode)) {
			$giftCard = Mage::getModel('giftcard/giftcard')->load($giftCode, 'code');
			if ($giftCard->isActive(true, true, true, true)) {
				$list           = Mage::getModel('giftcard/giftcard_list');
				$listCollection = $list->getCollection()
					->addFieldToFilter('customer_id', $this->_getSession()->getCustomerId())
					->addFieldToFilter('giftcard_id', $giftCard->getId());

				if (!$listCollection->getSize()) {
					try {
						$list->addData(array(
							'giftcard_id' => $giftCard->getId(),
							'customer_id' => $this->_getSession()->getCustomerId(),
							'added_at'    => Varien_Date::now(true)
						))
							->setId(null)
							->save();

						$success = true;
						$this->_getSession()->addSuccess($this->__('Gift Card Code added successfully.'));
					} catch (Exception $e) {
						$this->_getSession()->addError($e->getMessage());
					}
				}
			}
		}

		if (!$success) {
			$this->_getSession()->addError($this->__('Cannot add this code.'));
		}

		$this->_redirect('*');
	}

	public function viewAction()
	{
		$giftcardId = $this->getRequest()->getParam('id');
		if ($giftcardId) {
			$giftcard = Mage::getModel('giftcard/giftcard')->load($giftcardId);
			if ($giftcard->getId()) {
				Mage::register('giftcard_view_information', $giftcard);
			}
		}

		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_title($this->__('View Gift Card Detail'));
		$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('giftcard/');
		}
		$this->renderLayout();
	}

	public function removeAction()
	{
		$success = false;

		$giftCard = Mage::getModel('giftcard/giftcard')->load($this->getRequest()->getParam('id'));
		if ($giftcardId = $giftCard->getId()) {
			$cardInList = Mage::getModel('giftcard/giftcard_list')->getCollection()
				->addFieldToFilter('customer_id', $this->_getSession()->getCustomerId())
				->addFieldToFilter('giftcard_id', $giftcardId)
				->getFirstItem();

			if ($cardInList->getId()) {
				try {
					$cardInList->delete();
					$success = true;
					$this->_getSession()->addSuccess($this->__('Gift Card Code was removed from your list.'));
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		if (!$success) {
			$this->_getSession()->addError($this->__('Cannot remove this code.'));
		}

		$this->_redirect('*');
	}

	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}
}