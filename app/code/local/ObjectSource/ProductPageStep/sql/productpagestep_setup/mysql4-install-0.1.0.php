<?php
$this->startSetup();

$this->run("
CREATE TABLE `{$this->getTable('productpagestep/attribute')}` (
  `attribute_id` mediumint(8) unsigned NOT NULL auto_increment,
  `option_label` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `title1` varchar(255),
  `title2` varchar(255),
  `content1` text,
  `content2` text,
  PRIMARY KEY  (`attribute_id`),
  KEY `idx_optionlabel` (`option_label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->endSetup(); 