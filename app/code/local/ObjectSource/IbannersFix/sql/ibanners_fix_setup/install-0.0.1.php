<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('ibanners/banner'), 'url_target', 'text(64)');
$installer->endSetup();
