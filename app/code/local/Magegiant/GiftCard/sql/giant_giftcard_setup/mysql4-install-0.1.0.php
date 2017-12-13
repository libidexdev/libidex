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


$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'giftcard/giftcard'
 */
$installer->getConnection()->dropTable($installer->getTable('giftcard/giftcard'));
$table = $installer->getConnection()
	->newTable($installer->getTable('giftcard/giftcard'))
	->addColumn('giftcard_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity' => true,
		'unsigned' => true,
		'nullable' => false,
		'primary'  => true,
	), 'Card Id')
	->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable' => false,
	), 'Code')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Giftcard Name')
	->addColumn('active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable' => false,
		'default'  => '1',
	), 'Is Active')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable' => false,
		'default'  => '1',
	), 'Status')
	->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
		'default'  => '0.0000',
	), 'Gift Card Amount')
	->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Website Id')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Store Id')
	->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
		'nullable' => true,
	), 'Condition Serialized')
	->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
		'nullable' => true,
	), 'Action Serialized')
	->addColumn('conditions_description', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
		'nullable' => true,
	), 'Conditions Description')
	->addColumn('sender_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Sender Name')
	->addColumn('sender_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Sender Email')
	->addColumn('recipient_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Recipient Name')
	->addColumn('recipient_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Recipient Email')
	->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
		'nullable' => true,
	), 'Message')
	->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Email Sent')
	->addColumn('reminder_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Reminder Email Sent')
	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => true,
	), 'Item Id')
	->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
		'nullable' => true,
	), 'Order Increment Id')
	->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => true,
	), 'Item Id')
	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
		'nullable' => false,
	), 'Date Created')
	->addColumn('expired_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'Date Expires')
	->addColumn('schedule_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'Date Schedule')
	->setComment('Gift Card');
$installer->getConnection()->createTable($table);

/**
 * Create table 'giftcard/history'
 */
$installer->getConnection()->dropTable($installer->getTable('giftcard/history'));
$table = $installer->getConnection()
	->newTable($installer->getTable('giftcard/history'))
	->addColumn('history_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity' => true,
		'unsigned' => true,
		'nullable' => false,
		'primary'  => true,
	), 'History Id')
	->addColumn('giftcard_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Giftcard Id')
	->addColumn('giftcard_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable' => false,
	), 'Giftcard Code')
	->addColumn('action', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Action')
	->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
		'default'  => '0.0000',
	), 'Amount After Change')
	->addColumn('amount_change', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable' => false,
		'default'  => '0.0000',
	), 'Changed Amount')
	->addColumn('history_detail', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'History Detail')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'Updated At')
	->addForeignKey($installer->getFkName('giftcard/history', 'giftcard_id', 'giftcard/giftcard', 'giftcard_id'),
		'giftcard_id', $installer->getTable('giftcard/giftcard'), 'giftcard_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Giftcard History');
$installer->getConnection()->createTable($table);

/**
 * Create table 'giftcard/list'
 */
$installer->getConnection()->dropTable($installer->getTable('giftcard/list'));
$table = $installer->getConnection()
	->newTable($installer->getTable('giftcard/list'))
	->addColumn('list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity' => true,
		'unsigned' => true,
		'nullable' => false,
		'primary'  => true,
	), 'List Id')
	->addColumn('giftcard_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Giftcard Id')
	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
	), 'Customer Id')
	->addColumn('added_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'Added At')
	->addForeignKey($installer->getFkName('giftcard/list', 'giftcard_id', 'giftcard/giftcard', 'giftcard_id'),
		'giftcard_id', $installer->getTable('giftcard/giftcard'), 'giftcard_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($installer->getFkName('giftcard/list', 'customer_id', 'customer/entity', 'entity_id'),
		'customer_id', $installer->getTable('customer/entity'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Giftcard List');
$installer->getConnection()->createTable($table);

/**
 * Add Gift Card Attribute
 */
$this->addGiftCardAttribute();

$salesSetup = Mage::getResourceModel('sales/setup', 'giant_giftcard_setup');
$salesSetup->addAttribute('order', 'gift_cards', array('type' => 'text'))
	->addAttribute('order', 'base_giftcard_amount', array('type' => 'decimal'))
	->addAttribute('order', 'giftcard_amount', array('type' => 'decimal'))
	->addAttribute('order', 'base_giftcard_shipping_amount', array('type' => 'decimal'))
	->addAttribute('order', 'giftcard_shipping_amount', array('type' => 'decimal'))
	->addAttribute('order_item', 'base_giftcard_invoiced', array('type' => 'decimal'))
	->addAttribute('order_item', 'giftcard_invoiced', array('type' => 'decimal'))
	->addAttribute('order_item', 'base_giftcard_refunded', array('type' => 'decimal'))
	->addAttribute('order_item', 'giftcard_refunded', array('type' => 'decimal'))
	->addAttribute('order_item', 'gift_cards', array('type' => 'text'))
	->addAttribute('order_item', 'gift_cards_refunded', array('type' => 'text'))
	->addAttribute('order_item', 'base_giftcard_amount', array('type' => 'decimal'))
	->addAttribute('order_item', 'giftcard_amount', array('type' => 'decimal'))
	->addAttribute('invoice', 'base_giftcard_amount', array('type' => 'decimal'))
	->addAttribute('invoice', 'giftcard_amount', array('type' => 'decimal'))
	->addAttribute('creditmemo', 'base_giftcard_amount', array('type' => 'decimal'))
	->addAttribute('creditmemo', 'giftcard_amount', array('type' => 'decimal'));

$installer->endSetup();
