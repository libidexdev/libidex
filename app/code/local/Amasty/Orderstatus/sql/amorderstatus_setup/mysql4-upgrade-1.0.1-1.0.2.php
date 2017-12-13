<?php
/**
* @copyright Amasty.
*/

$installer = $this;

$installer->startSetup();


$installer->run("
ALTER TABLE `{$this->getTable('amorderstatus/status')}` ADD `is_system` TINYINT( 1 ) UNSIGNED NOT NULL ;
");

$installer->endSetup(); 