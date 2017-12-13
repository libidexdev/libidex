<?php
/**
 * Setup scripts, add new column and fulfills
 * its values to existing rows
 *
 */
/* @var $this Mage_Sales_Model_Mysql4_Setup */
$this->startSetup();
// Add column to grid table
$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'appr',
    "varchar(1) not null default ''"
);
// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'appr',
    'appr'
);

// Add column to grid table
$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'code',
    "varchar(1) not null default ''"
);
// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'code',
    'code'
);

$this->endSetup();
