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
 * @package     Magegiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardcredit Grid Block
 * 
 * @category    Magegiant
 * @package     Magegiant_GiftCardCredit
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardCredit_Block_Adminhtml_Credit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardcreditGrid');
        $this->setDefaultSort('account_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magegiant_GiftCardCredit_Block_Adminhtml_Giftcardcredit_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('giftcardcredit/account')->getCollection();

		$collection->getSelect()
			->join(
				array('ct' => $collection->getTable('customer/entity')),
				'main_table.customer_id = ct.entity_id',
				array('*')
			);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magegiant_GiftCardCredit_Block_Adminhtml_Giftcardcredit_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('account_id', array(
            'header'    => Mage::helper('giftcardcredit')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'account_id',
        ));

        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('giftcardcredit')->__('Customer'),
            'align'     =>'left',
            'index'     => 'customer_id',
			'renderer'     => 'giftcardcredit/adminhtml_credit_renderer_customer'
        ));

		$this->addColumn('balance', array(
			'header'        => Mage::helper('giftcardcredit')->__('Balance'),
			'width'         => 100,
			'align'         => 'right',
			'index'         => 'balance',
			'filter_index'  => 'main_table.balance',
			'type'          => 'price',
			'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode()
		));

		$this->addColumn('website_id', array(
			'header'       => Mage::helper('giftcardcredit')->__('Website'),
			'width'        => 100,
			'index'        => 'website_id',
			'filter_index' => 'main_table.website_id',
			'type'         => 'options',
			'options'      => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
		));

		$this->addColumn('created_at', array(
			'header'       => Mage::helper('giftcardcredit')->__('Created At'),
			'width'        => 120,
			'type'         => 'date',
			'index'        => 'created_at',
			'filter_index' => 'main_table.created_at',
		));

        $this->addExportType('*/*/exportCsv', Mage::helper('giftcardcredit')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('giftcardcredit')->__('XML'));

        return parent::_prepareColumns();
    }
    

    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}