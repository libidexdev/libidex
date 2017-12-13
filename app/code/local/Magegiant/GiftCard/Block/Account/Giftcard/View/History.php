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

class Magegiant_GiftCard_Block_Account_Giftcard_View_History extends Magegiant_GiftCard_Block_Abstract
{
	protected function _construct()
	{
		parent::_construct();

		$collection = Mage::getModel('giftcard/history')->getCollection()
			->addFieldToFilter('giftcard_id', $this->getRequest()->getParam('id'))
			->setOrder('history_id', 'desc');

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
}