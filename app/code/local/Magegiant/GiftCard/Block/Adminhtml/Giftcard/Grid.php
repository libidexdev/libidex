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
class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('giftcardGrid');
		$this->setDefaultSort('giftcard_id');
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
		$collection = Mage::getModel('giftcard/giftcard')->getCollection();

		Mage::dispatchEvent('magegiant_giftcard_grid_prepare_collection', array(
			'collection' => $collection
		));

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	/**
	 * prepare columns for this grid
	 *
	 * @return Magegiant_GiftCard_Block_Adminhtml_Giftcard_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('giftcard_id', array(
			'header' => Mage::helper('giftcard')->__('ID'),
			'align'  => 'right',
			'width'  => 50,
			'index'  => 'giftcard_id',
		));

		$this->addColumn('code', array(
			'header'       => Mage::helper('giftcard')->__('Gift Code'),
			'align'        => 'left',
			'index'        => 'code',
			'filter_index' => 'main_table.code',
		));

		$this->addColumn('name', array(
			'header'       => Mage::helper('giftcard')->__('Name'),
			'align'        => 'left',
			'index'        => 'name',
			'filter_index' => 'main_table.name',
		));

		$this->addColumn('amount', array(
			'header'        => Mage::helper('giftcard')->__('Balance'),
			'width'         => 100,
			'align'         => 'right',
			'index'         => 'amount',
			'filter_index'  => 'main_table.amount',
			'type'          => 'price',
			'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode()
		));

		$this->addColumn('status', array(
			'header'       => Mage::helper('giftcard')->__('Status'),
			'align'        => 'left',
			'width'        => 80,
			'index'        => 'status',
			'filter_index' => 'main_table.status',
			'type'         => 'options',
			'options'      => Mage::getModel('giftcard/giftcard')->getStatusArray()
		));

		$this->addColumn('website', array(
			'header'       => Mage::helper('giftcard')->__('Website'),
			'width'        => 100,
			'index'        => 'website_id',
			'filter_index' => 'main_table.website_id',
			'type'         => 'options',
			'options'      => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
		));

		$this->addColumn('created_at', array(
			'header'       => Mage::helper('giftcard')->__('Created At'),
			'width'        => 120,
			'type'         => 'date',
			'index'        => 'created_at',
			'filter_index' => 'main_table.created_at',
		));

		$this->addColumn('expired_at', array(
			'header'       => Mage::helper('giftcard')->__('Expiration At'),
			'width'        => 120,
			'type'         => 'date',
			'index'        => 'expired_at',
			'filter_index' => 'main_table.expired_at',
			'default'      => '--',
		));

		$this->addColumn('active', array(
			'header'       => Mage::helper('giftcard')->__('Active'),
			'align'        => 'left',
			'width'        => 50,
			'index'        => 'active',
			'filter_index' => 'main_table.state',
			'type'         => 'options',
			'options'      => array(
				Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE   => Mage::helper('giftcard')->__('Yes'),
				Magegiant_GiftCard_Model_Giftcard::STATE_INACTIVE => Mage::helper('giftcard')->__('No'),
			)
		));

		Mage::dispatchEvent('magegiant_giftcard_grid_prepare_column', array(
			'grid' => $this
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('giftcard')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('giftcard')->__('XML'));

		return parent::_prepareColumns();
	}

	/**
	 * prepare mass action for this grid
	 *
	 * @return Magegiant_GiftCard_Block_Adminhtml_Giftcard_Grid
	 */
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('giftcard_id');
		$this->getMassactionBlock()->setFormFieldName('giftcard');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'   => Mage::helper('giftcard')->__('Delete'),
			'url'     => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('giftcard')->__('Are you sure?')
		));

		$this->getMassactionBlock()->addItem('email', array(
			'label' => Mage::helper('giftcard')->__('Send Email'),
			'url'   => $this->getUrl('*/*/massEmail')
		));

		$this->getMassactionBlock()->addItem('active', array(
			'label'      => Mage::helper('giftcard')->__('Change active'),
			'url'        => $this->getUrl('*/*/massState', array('_current' => true)),
			'additional' => array(
				'visibility' => array(
					'name'   => 'active',
					'type'   => 'select',
					'class'  => 'required-entry',
					'label'  => Mage::helper('giftcard')->__('To'),
					'values' => array(
						Magegiant_GiftCard_Model_Giftcard::STATE_ACTIVE   => Mage::helper('giftcard')->__('Yes'),
						Magegiant_GiftCard_Model_Giftcard::STATE_INACTIVE => Mage::helper('giftcard')->__('No'),
					)
				))
		));

		Mage::dispatchEvent('magegiant_giftcard_grid_prepare_massaction', array(
			'grid'  => $this,
			'block' => $this->getMassactionBlock()
		));

		return $this;
	}

	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}

//	protected function _prepareLayout()
//	{
//		$this->setChild('import_button',
//			$this->getLayout()->createBlock('adminhtml/widget_button')
//				->setData(array(
//					'label'   => Mage::helper('adminhtml')->__('Import'),
//					'onclick' => $this->getJsObjectName() . '.doImport(\'' . $this->getUrl('*/*/import') . '\')',
//					'class'   => 'task',
//					'style'   => 'margin-left: 5px'
//				))
//		);
//
//		return parent::_prepareLayout();
//	}
//
//	public function getExportButtonHtml()
//	{
//		$html = $this->getChildHtml('export_button');
//		$html .= $this->getChildHtml('import_button');
//
//		return $html;
//	}
}