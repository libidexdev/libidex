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

class Magegiant_GiftCard_Block_Account_Giftcard_List extends Magegiant_GiftCard_Block_Abstract
{
	protected function _construct()
	{
		parent::_construct();

		$collection = Mage::getModel('giftcard/giftcard_list')->getCollection()
			->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomerId());

		$collection->getSelect()
			->join(
				array('gc' => $collection->getTable('giftcard/giftcard')),
				"main_table.giftcard_id = gc.giftcard_id",
				array('*'))
			->order('added_at DESC');

		$this->setCollection($collection);
	}

	public function _prepareLayout()
	{
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'giftcard_list_pager')
			->setCollection($this->getCollection());
		$this->setChild('giftcard_list_pager', $pager);

		return $this;
	}

	public function getPagerHtml()
	{
		return $this->getChildHtml('giftcard_list_pager');
	}

	public function getActionHtml($card)
	{
		$actions = new Varien_Object();

		if ($this->canView($card)) {
			$actions->setData('view', array(
				'label'  => $this->__('View'),
				'action' => $this->getUrl('giftcard/index/view', array('id' => $card->getGiftcardId()))
			));
		}

		if ($this->canRemove($card)) {
			$actions->setData('remove', array(
				'label'  => $this->__('Remove'),
				'action' => $this->getUrl('giftcard/index/remove', array('id' => $card->getGiftcardId()))
			));
		}

		Mage::dispatchEvent('giftcard_account_add_actions', array(
			'card'   => $card,
			'action' => $actions
		));

		$html = '';
		foreach ($actions->getData() as $action) {
			if ($html != '') {
				$html .= ' | ';
			}

			$html .= '<a href="' . $action['action'] . '">' . $action['label'] . '</a>';
		}

		return $html;
	}

	public function canView($card)
	{
		return true;
	}

	public function canRemove($card)
	{
		return true;
	}
}