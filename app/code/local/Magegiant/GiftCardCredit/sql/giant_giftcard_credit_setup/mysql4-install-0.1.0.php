<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'giftcard/credit'
 */
$installer->getConnection()->dropTable($installer->getTable('giftcardcredit/account'));
$table = $installer->getConnection()
	->newTable($installer->getTable('giftcardcredit/account'))
	->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity' => true,
		'unsigned' => true,
		'nullable' => false,
		'primary'  => true,
	), 'Credit Id')
	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Customer Id')
	->addColumn('balance', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
		'default'  => '0.0000',
	), 'Customer Balance')
	->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Website Id')
	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'Created At')
	->addForeignKey($installer->getFkName('giftcardcredit/account', 'customer_id', 'customer/entity', 'entity_id'),
		'customer_id', $installer->getTable('customer/entity'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Giftcard Credit');
$installer->getConnection()->createTable($table);

/**
 * Add Gift Card Attribute
 */
$this->addGiftCardAttribute();

$salesSetup = Mage::getResourceModel('sales/setup', 'giant_giftcard_credit_setup');
$salesSetup->addAttribute('order', 'base_giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('order', 'giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('order', 'base_giftcard_credit_shipping_amount', array('type' => 'decimal'))
	->addAttribute('order', 'giftcard_credit_shipping_amount', array('type' => 'decimal'))
	->addAttribute('order_item', 'base_giftcard_credit_invoiced', array('type' => 'decimal'))
	->addAttribute('order_item', 'giftcard_credit_invoiced', array('type' => 'decimal'))
	->addAttribute('order_item', 'base_giftcard_credit_refunded', array('type' => 'decimal'))
	->addAttribute('order_item', 'giftcard_credit_refunded', array('type' => 'decimal'))
	->addAttribute('order_item', 'base_giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('order_item', 'giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('invoice', 'base_giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('invoice', 'giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('creditmemo', 'base_giftcard_credit_amount', array('type' => 'decimal'))
	->addAttribute('creditmemo', 'giftcard_credit_amount', array('type' => 'decimal'));

$installer->getConnection()->addColumn($this->getTable('giftcard/giftcard'), 'allow_redeem', 'int(5) default 1');

$installer->endSetup();

