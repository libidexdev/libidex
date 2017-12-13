<?php
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$connection->addColumn($this->getTable('sales/quote_item'), 'base_rapidservice_amount', 'decimal(12,4) null');
$connection->addColumn($this->getTable('sales/quote_item'), 'rapidservice_amount', 'decimal(12,4) null');

$connection->addColumn($this->getTable('sales/quote_address_item'), 'base_rapidservice_amount', 'decimal(12,4) null');
$connection->addColumn($this->getTable('sales/quote_address_item'), 'rapidservice_amount', 'decimal(12,4) null');

$connection->addColumn($this->getTable('sales/quote_address'), 'base_rapidservice_amount', 'decimal(12,4) null');
$connection->addColumn($this->getTable('sales/quote_address'), 'rapidservice_amount', 'decimal(12,4) null');

$connection->addColumn($this->getTable('sales/order_item'), 'base_rapidservice_amount', 'decimal(12,4) null');
$connection->addColumn($this->getTable('sales/order_item'), 'rapidservice_amount', 'decimal(12,4) null');

$connection->addColumn($this->getTable('sales/order'), 'base_rapidservice_amount', 'decimal(12,4) null');
$connection->addColumn($this->getTable('sales/order'), 'rapidservice_amount', 'decimal(12,4) null');

// Adds rapid service attribute as a product flag (yes/no).
$installer->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'os_rapidservice',
    array(
        'label'                      => Mage::helper('os_rapidservice')->__('Available for Rapid Order'),
        'group'                      => 'General',
        'type'                       => 'int',
        'input'                      => 'boolean',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'required'                   => false,
        'searchable'                 => true,
        'used_in_product_listing'    => true,
        'sort_order'                 => '50',
        'used_for_promo_rules'       => true,
    ));

$installer->endSetup();
