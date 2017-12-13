<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 19/09/16
 * Time: 11:39
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('lexel_delivery_comments'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true), 'Entity Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false), 'Order Id')
    ->addColumn('order_status_history_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false), 'Order Status History Id')
    ->addColumn('sent', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false), 'Sent');

$installer->getConnection()->createTable($table);

$installer->endSetup();