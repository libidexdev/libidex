<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$webforms_table = 'webforms';

$edition = 'CE';
$version = explode('.', Mage::getVersion());
if ($version[1] >= 9)
	$edition = 'EE';

if((float)substr(Mage::getversion(),0,3)>1.1 || $edition == 'EE')
	$webforms_table = $this->getTable('webforms/webforms');

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/results', 'webform_id', 'webforms/webforms', 'id'),
    $installer->getTable('webforms/results'),
    'webform_id',
    $installer->getTable('webforms/webforms'),
    'id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/results_values', 'result_id', 'webforms/results', 'id'),
    $installer->getTable('webforms/results_values'),
    'result_id',
    $installer->getTable('webforms/results'),
    'id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/results_values', 'field_id', 'webforms/fields', 'id'),
    $installer->getTable('webforms/results_values'),
    'field_id',
    $installer->getTable('webforms/fields'),
    'id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/fields', 'webform_id', 'webforms/webforms', 'id'),
    $installer->getTable('webforms/fields'),
    'webform_id',
    $installer->getTable('webforms/webforms'),
    'id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/fieldsets', 'webform_id', 'webforms/webforms', 'id'),
    $installer->getTable('webforms/fieldsets'),
    'webform_id',
    $installer->getTable('webforms/webforms'),
    'id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/logic', 'field_id', 'webforms/fields', 'id'),
    $installer->getTable('webforms/logic'),
    'field_id',
    $installer->getTable('webforms/fields'),
    'id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('webforms/message', 'field_id', 'webforms/results', 'id'),
    $installer->getTable('webforms/message'),
    'result_id',
    $installer->getTable('webforms/results'),
    'id'
);

$installer->endSetup();