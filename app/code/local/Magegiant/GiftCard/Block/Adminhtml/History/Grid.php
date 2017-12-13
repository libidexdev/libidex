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

class Magegiant_GiftCard_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('historyGrid');
		$this->setDefaultSort('history_id');
		$this->setDefaultDir('DESC');
		$this->setUseAjax(true);
	}

	/**
	 * prepare collection for block to display
	 *
	 * @return Magegiant_GiftCard_Block_Adminhtml_Giftcard_Grid
	 */
	protected function _prepareCollection()
	{
		if(!$this->getCollection()) {
			$collection = Mage::getModel('giftcard/history')->getCollection();

			Mage::dispatchEvent('magegiant_giftcard_history_grid_prepare_collection', array(
				'collection' => $collection
			));

			$this->setCollection($collection);
		}

		return parent::_prepareCollection();
	}

	/**
	 * prepare columns for this grid
	 *
	 * @return Magegiant_GiftCard_Block_Adminhtml_Giftcard_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('history_id', array(
			'header' => Mage::helper('giftcard')->__('ID'),
			'align'  => 'right',
			'width'  => 50,
			'index'  => 'history_id',
		));

		$this->addColumn('updated_at', array(
			'header'       => Mage::helper('giftcard')->__('Date'),
			'width'        => 120,
			'type'         => 'date',
			'index'        => 'updated_at',
			'filter_index' => 'main_table.updated_at',
		));

		$this->addColumn('giftcard_code', array(
			'header'       => Mage::helper('giftcard')->__('Gift Code'),
			'align'        => 'left',
			'width'        => 180,
			'index'        => 'giftcard_code',
			'sortable'     => false,
			'filter_index' => 'main_table.giftcard_code',
		));

		$this->addColumn('history_action', array(
			'header'       => Mage::helper('giftcard')->__('Action'),
			'align'        => 'left',
			'width'        => 80,
			'index'        => 'action',
			'filter_index' => 'main_table.action',
			'type'         => 'options',
			'sortable'     => false,
			'options'      => Mage::getModel('giftcard/history')->getActionArray()
		));

		$this->addColumn('amount_change', array(
			'header'        => Mage::helper('giftcard')->__('Balance Change'),
			'width'         => 100,
			'align'         => 'right',
			'index'         => 'amount_change',
			'filter_index'  => 'main_table.amount_change',
			'type'          => 'price',
			'sortable'      => false,
			'filter'        => false,
			'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode()
		));

		$this->addColumn('amount', array(
			'header'        => Mage::helper('giftcard')->__('Balance'),
			'width'         => 100,
			'align'         => 'right',
			'index'         => 'amount',
			'filter_index'  => 'main_table.amount',
			'type'          => 'price',
			'sortable'      => false,
			'filter'        => false,
			'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode()
		));

		$this->addColumn('history_detail', array(
			'header'       => Mage::helper('giftcard')->__('Additional Information'),
			'align'        => 'left',
			'index'        => 'history_detail',
			'filter_index' => 'main_table.history_detail',
			'sortable'     => false,
		));

		Mage::dispatchEvent('magegiant_giftcard_history_grid_prepare_column', array(
			'grid' => $this
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('giftcard')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('giftcard')->__('XML'));

		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}