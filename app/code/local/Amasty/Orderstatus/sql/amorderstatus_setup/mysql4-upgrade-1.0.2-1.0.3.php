<?php
/**
* @copyright Amasty.
*/

$installer = $this;
$installer->startSetup();


$installer->run("
ALTER TABLE `{$this->getTable('amorderstatus/status')}` CHANGE `parent_state` `parent_state` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
");

$installer->endSetup(); 