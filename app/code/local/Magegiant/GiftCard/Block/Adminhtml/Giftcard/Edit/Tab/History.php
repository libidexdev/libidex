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

class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_History
	extends Magegiant_GiftCard_Block_Adminhtml_History_Grid
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('history_session');
	}

	/**
	 * Prepare content for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return Mage::helper('giftcard')->__('History');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('giftcard')->__('History');
	}

	/**
	 * Returns status flag about this tab can be showen or not
	 *
	 * @return true
	 */
	public function canShowTab()
	{
		$giftcard = Mage::registry('current_giftcard');

		if ($giftcard && $giftcard->getId()) {
			return true;
		}

		return false;
	}

	/**
	 * Returns status flag about this tab hidden or not
	 *
	 * @return true
	 */
	public function isHidden()
	{
		return false;
	}

	protected function _prepareCollection()
	{
		$giftcard = Mage::getModel('giftcard/giftcard')->load($this->getRequest()->getParam('id'));

		$collection = Mage::getModel('giftcard/history')->getCollection()
			->addFieldToFilter('giftcard_code', $giftcard->getCode());

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		parent::_prepareColumns();

		$this->removeColumn('giftcard_code');

		$this->_exportTypes = array();

		return $this;
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/historygrid', array('_current' => true));
	}
}