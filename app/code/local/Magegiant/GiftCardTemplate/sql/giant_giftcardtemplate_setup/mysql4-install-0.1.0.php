<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('giftcardtemplate/design')};
CREATE TABLE {$this->getTable('giftcardtemplate/design')} (
  `design_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `website_ids` text,
  `customer_group_ids` text,
  `sort_order` tinyint(5) DEFAULT NULL,
  `number_items` int(11) DEFAULT NULL,
  `status` tinyint(5) DEFAULT 1,
  PRIMARY KEY (`design_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS {$this->getTable('giftcardtemplate/design_items')};
CREATE TABLE {$this->getTable('giftcardtemplate/design_items')} (
  `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `source_file` text,
  `thumb_file` text,
  `video_url` text,
  `format_id` smallint(5) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `is_default` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('giftcardtemplate/design_items_detail')};
CREATE TABLE {$this->getTable('giftcardtemplate/design_items_detail')} (
  `detail_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `design_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `design_id` (`design_id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (`design_id`) REFERENCES {$this->getTable('giftcardtemplate/design')} (`design_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES {$this->getTable('giftcardtemplate/design_items')} (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->getConnection()->addColumn($installer->getTable('giftcard/giftcard'), 'uploaded_html', 'TEXT NULL');
$installer->getConnection()->addColumn($installer->getTable('giftcard/giftcard'), 'video_url', 'TEXT NULL');
$installer->getConnection()->addColumn($installer->getTable('giftcard/giftcard'), 'selected_item', 'int(11) NULL');
$installer->getConnection()->addColumn($installer->getTable('giftcard/giftcard'), 'message_box', 'tinyint(1) NULL');
$installer->getConnection()->addColumn($installer->getTable('giftcard/giftcard'), 'price_box', 'tinyint(1) NULL');
/*Add Giftcard Attribute*/
$installer->addGiftCardAttribute();
$installer->endSetup();

