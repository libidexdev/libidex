<?php
/** @var ObjectSource_MalaysiaInvoice_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();
$this->addAttribute('catalog_product', 'malaysia_price', array(
    'type'       => 'decimal',
    'input'      => 'price',
    'label'      => 'Malaysia Price',
    'sort_order' => 1000,
    'required'   => false,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'backend'    => 'catalog/product_attribute_backend_price',
    'group'     => 'Prices'
));


/**
 *  Malaysia Invoice Table.
 */
$table = $connection->newTable($installer->getTable('os_malaysia_invoice/invoice'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity ID')
    ->addColumn('malaysia_total_gbp', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Malaysia Price in GBP')
    ->addColumn('malaysia_total_usd', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Malaysia Price in USD')
    ->addColumn('exchange_rate_gbp_usd', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Exchange Rate')
    ->addColumn('invoice_reference', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Invoice Reference')
    ->addColumn('awb_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Airway Bill Number')
    ->addColumn('order_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '4k', array(
    ), 'Order Ids')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Update Time')
    ->setComment('Malaysia Invoice Table');
$installer->getConnection()->createTable($table);

/**
 *  Link between Malaysia Invoices and Magento Sales Order Items.
 */
$table = $connection->newTable($installer->getTable('os_malaysia_invoice/invoice_item'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity ID')
    ->addColumn('invoice_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity ID')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Item ID')
    ->addColumn('malaysia_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Malaysia Price')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Product Name')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'SKU')
    ->addColumn('increment_order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Magento Order Reference')
    ->addColumn('color', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Color Option')
    ->addColumn('size', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Size Option')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Item Quantity')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '32k', array(
    ), 'Comment')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Update Time')
    ->addIndex($installer->getIdxName('os_malaysia_invoice/invoice_item', array('invoice_id')),
        array('item_id'))
    ->addIndex($installer->getIdxName('os_malaysia_invoice/invoice_item', array('item_id')),
        array('item_id'))
    ->addForeignKey(
        $installer->getFkName(
            'os_malaysia_invoice/invoice_item',
            'invoice_id',
            'os_malaysia_invoice/invoice',
            'entity_id'
        ),
        'invoice_id', $installer->getTable('os_malaysia_invoice/invoice'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'os_malaysia_invoice/invoice_item',
            'item_id',
            'sales/order_item',
            'item_id'
        ),
        'item_id', $installer->getTable('sales/order_item'), 'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Malaysia Invoice to Sales Item Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
