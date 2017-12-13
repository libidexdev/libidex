<?php

/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */
class Magegiant_GiftCardTemplate_Model_Mysql4_Setup extends Magegiant_GiftCard_Model_Mysql4_Setup
{

    public function addGiftCardAttribute()
    {
        $setup         = Mage::getResourceModel('catalog/setup', 'giant_giftcardtemplate_setup');
        $entityTypeId  = $setup->getEntityTypeId('catalog_product');
        $attributeSets = $setup->_conn->fetchAll('select * from ' . $this->getTable('eav/attribute_set') . ' where entity_type_id=?', $entityTypeId);
        foreach ($attributeSets as $attributeSet) {
            $setup->addAttributeGroup($entityTypeId, $attributeSet['attribute_set_id'], 'Gift Card Templates', 2);
        }
        $attributes = $this->getTemplateAttributeArray();
        foreach ($attributes as $code => $data) {
            $this->addAttribute($setup, $code, $data);
        }
        $this->setApplyToAttribute($setup);

        return $this;
    }

    public function getDefaultAttributeArray()
    {
        return array(
            'group'                   => 'Gift Card Templates',
            'type'                    => 'decimal',
            'backend'                 => '',
            'frontend'                => '',
            'label'                   => '',
            'input'                   => 'price',
            'class'                   => 'general',
            'source'                  => '',
            'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => true,
            'default'                 => '',
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'unique'                  => false,
            'apply_to'                => Magegiant_GiftCard_Model_Giftcard::PRODUCT_TYPE,
            'is_configurable'         => false,
            'used_in_product_listing' => true,
            'sort_order'              => 0,
        );
    }

    public function getTemplateAttributeArray()
    {
        return array(
            'giftcard_design_ids'  => array(
                'type'       => 'text',
                'label'      => 'Design Templates',
                'input'      => 'multiselect',
                'source'     => 'giftcardtemplate/source_design_type',
                'backend'    => 'eav/entity_attribute_backend_array',
                'sort_order' => 10,
            ),
            'giftcard_design_all'  => array(
                'type'       => 'int',
                'label'      => 'Allow Selecting Other Designs',
                'input'      => 'select',
                'source'     => 'giftcard/source_yesno',
                'sort_order' => 20,
            ),
            'giftcard_upload_area' => array(
                'type'       => 'int',
                'label'      => 'Show Upload Section',
                'input'      => 'select',
                'source'     => 'giftcard/source_yesno',
                'sort_order' => 30,
            ),
            'giftcard_message_box' => array(
                'type'       => 'int',
                'label'      => 'Show Customer Message Section',
                'input'      => 'select',
                'source'     => 'giftcard/source_yesno',
                'sort_order' => 40,
            ),
            'giftcard_price_box'   => array(
                'type'       => 'int',
                'label'      => 'Show Price Section',
                'input'      => 'select',
                'source'     => 'giftcard/source_yesno',
                'sort_order' => 50,
            ),
        );

    }

    public function insertDefaultData()
    {
        $setup = Mage::getResourceModel('core/setup', 'giant_giftcardtemplate_setup');
        /*Insert Design*/
        $setup->run("
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (1,'Black Friday','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (2,'Lunar New Year','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (3,'Christmas','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (4,'Cyber Monday','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (6,'Earth Day','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (7,'Father\'s Day','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (9,'Halloween','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (10,'July 4th','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (11,'Mother\'s Day','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (12,'Saint Patrick\'s Day','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (13,'Thanks giving','1','0,1,2,4,5',0,1,1);
            insert  into {$this->getTable('giftcardtemplate/design')}(`design_id`,`name`,`website_ids`,`customer_group_ids`,`sort_order`,`number_items`,`status`) values (14,'Valentine\'s Day','1','0,1,2,4,5',0,1,1);

        ");
        $setup->run("
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (63,'Christmas1','Christmas1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (64,'Christmas10','Christmas10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (65,'Christmas11','Christmas11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (66,'Christmas12','Christmas12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (67,'Christmas13','Christmas13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (68,'Christmas14','Christmas14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (69,'Christmas15','Christmas15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (70,'Christmas16','Christmas16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (71,'Christmas17','Christmas17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (72,'Christmas18','Christmas18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (73,'Christmas19','Christmas19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (74,'Christmas2','Christmas2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (75,'Christmas20','Christmas20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (76,'Christmas21','Christmas21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (77,'Christmas22','Christmas22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (78,'Christmas23','Christmas23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (79,'Christmas24','Christmas24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (80,'Christmas25','Christmas25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (81,'Christmas26','Christmas26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (82,'Christmas27','Christmas27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (83,'Christmas28','Christmas28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (84,'Christmas29','Christmas29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (85,'Christmas3','Christmas3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (86,'Christmas30','Christmas30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (87,'Christmas4','Christmas4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (88,'Christmas5','Christmas5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (89,'Christmas6','Christmas6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (90,'Christmas7','Christmas7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (91,'Christmas8','Christmas8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (92,'Christmas9','Christmas9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (93,'Cyber1','Cyber1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (94,'Cyber10','Cyber10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (95,'Cyber11','Cyber11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (96,'Cyber12','Cyber12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (97,'Cyber13','Cyber13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (98,'Cyber14','Cyber14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (99,'Cyber2','Cyber2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (100,'Cyber3','Cyber3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (101,'Cyber4','Cyber4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (102,'Cyber5','Cyber5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (103,'Cyber6','Cyber6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (104,'Cyber7','Cyber7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (105,'Cyber8','Cyber8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (106,'Cyber9','Cyber9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (107,'EarthDay1','EarthDay1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (108,'EarthDay10','EarthDay10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (109,'EarthDay11','EarthDay11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (110,'EarthDay12','EarthDay12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (111,'EarthDay13','EarthDay13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (112,'EarthDay14','EarthDay14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (113,'EarthDay15','EarthDay15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (114,'EarthDay16','EarthDay16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (115,'EarthDay17','EarthDay17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (116,'EarthDay18','EarthDay18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (117,'EarthDay19','EarthDay19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (118,'EarthDay2','EarthDay2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (119,'EarthDay20','EarthDay20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (120,'EarthDay21','EarthDay21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (121,'EarthDay22','EarthDay22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (122,'EarthDay23','EarthDay23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (123,'EarthDay24','EarthDay24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (124,'EarthDay25','EarthDay25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (125,'EarthDay26','EarthDay26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (126,'EarthDay27','EarthDay27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (127,'EarthDay28','EarthDay28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (128,'EarthDay29','EarthDay29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (129,'EarthDay3','EarthDay3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (130,'EarthDay30','EarthDay30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (131,'EarthDay4','EarthDay4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (132,'EarthDay5','EarthDay5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (133,'EarthDay6','EarthDay6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (134,'EarthDay7','EarthDay7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (135,'EarthDay8','EarthDay8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (136,'EarthDay9','EarthDay9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (137,'July4th1','July4th1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (138,'July4th10','July4th10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (139,'July4th11','July4th11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (140,'July4th12','July4th12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (141,'July4th13','July4th13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (142,'July4th14','July4th14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (143,'July4th15','July4th15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (144,'July4th16','July4th16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (145,'July4th17','July4th17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (146,'July4th18','July4th18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (147,'July4th19','July4th19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (148,'July4th2','July4th2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (149,'July4th20','July4th20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (150,'July4th21','July4th21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (151,'July4th22','July4th22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (152,'July4th23','July4th23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (153,'July4th24','July4th24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (154,'July4th25','July4th25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (155,'July4th26','July4th26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (156,'July4th3','July4th3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (157,'July4th4','July4th4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (158,'July4th5','July4th5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (159,'July4th6','July4th6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (160,'July4th7','July4th7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (161,'July4th8','July4th8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (162,'July4th9','July4th9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (163,'black1','black1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (164,'black10','black10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (165,'black11','black11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (166,'black12','black12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (167,'black13','black13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (168,'black14','black14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (169,'black15','black15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (170,'black16','black16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (171,'black17','black17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (172,'black18','black18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (173,'black19','black19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (174,'black2','black2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (175,'black20','black20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (176,'black3','black3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (177,'black4','black4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (178,'black5','black5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (179,'black6','black6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (180,'black7','black7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (181,'black8','black8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (182,'black9','black9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (183,'fatherday1','fatherday1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (184,'fatherday10','fatherday10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (185,'fatherday11','fatherday11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (186,'fatherday12','fatherday12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (187,'fatherday13','fatherday13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (188,'fatherday14','fatherday14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (189,'fatherday15','fatherday15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (190,'fatherday16','fatherday16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (191,'fatherday17','fatherday17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (192,'fatherday18','fatherday18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (193,'fatherday19','fatherday19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (194,'fatherday2','fatherday2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (195,'fatherday20','fatherday20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (196,'fatherday21','fatherday21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (197,'fatherday22','fatherday22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (198,'fatherday23','fatherday23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (199,'fatherday24','fatherday24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (200,'fatherday25','fatherday25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (201,'fatherday26','fatherday26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (202,'fatherday27','fatherday27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (203,'fatherday28','fatherday28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (204,'fatherday29','fatherday29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (205,'fatherday3','fatherday3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (206,'fatherday30','fatherday30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (207,'fatherday31','fatherday31.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (208,'fatherday32','fatherday32.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (209,'fatherday33','fatherday33.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (210,'fatherday4','fatherday4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (211,'fatherday5','fatherday5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (212,'fatherday6','fatherday6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (213,'fatherday7','fatherday7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (214,'fatherday8','fatherday8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (215,'fatherday9','fatherday9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (216,'halloween1','halloween1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (217,'halloween10','halloween10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (218,'halloween11','halloween11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (219,'halloween12','halloween12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (220,'halloween13','halloween13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (221,'halloween14','halloween14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (222,'halloween15','halloween15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (223,'halloween16','halloween16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (224,'halloween17','halloween17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (225,'halloween18','halloween18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (226,'halloween19','halloween19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (227,'halloween2','halloween2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (228,'halloween20','halloween20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (229,'halloween21','halloween21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (230,'halloween22','halloween22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (231,'halloween23','halloween23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (232,'halloween24','halloween24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (233,'halloween25','halloween25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (234,'halloween26','halloween26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (235,'halloween27','halloween27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (236,'halloween28','halloween28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (237,'halloween29','halloween29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (238,'halloween3','halloween3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (239,'halloween30','halloween30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (240,'halloween31','halloween31.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (241,'halloween4','halloween4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (242,'halloween5','halloween5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (243,'halloween6','halloween6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (244,'halloween7','halloween7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (245,'halloween8','halloween8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (246,'halloween9','halloween9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (247,'mothersDay1','mothersDay1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (248,'mothersDay10','mothersDay10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (249,'mothersDay11','mothersDay11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (250,'mothersDay12','mothersDay12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (251,'mothersDay13','mothersDay13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (252,'mothersDay14','mothersDay14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (253,'mothersDay15','mothersDay15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (254,'mothersDay16','mothersDay16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (255,'mothersDay17','mothersDay17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (256,'mothersDay18','mothersDay18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (257,'mothersDay19','mothersDay19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (258,'mothersDay2','mothersDay2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (259,'mothersDay20','mothersDay20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (260,'mothersDay21','mothersDay21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (261,'mothersDay22','mothersDay22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (262,'mothersDay23','mothersDay23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (263,'mothersDay24','mothersDay24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (264,'mothersDay25','mothersDay25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (265,'mothersDay26','mothersDay26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (266,'mothersDay27','mothersDay27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (267,'mothersDay28','mothersDay28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (268,'mothersDay29','mothersDay29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (269,'mothersDay3','mothersDay3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (270,'mothersDay30','mothersDay30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (271,'mothersDay4','mothersDay4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (272,'mothersDay5','mothersDay5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (273,'mothersDay6','mothersDay6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (274,'mothersDay7','mothersDay7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (275,'mothersDay8','mothersDay8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (276,'mothersDay9','mothersDay9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (277,'new_year','new_year.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (278,'new_year1','new_year1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (279,'new_year10','new_year10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (280,'new_year11','new_year11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (281,'new_year12','new_year12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (282,'new_year13','new_year13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (283,'new_year14','new_year14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (284,'new_year15','new_year15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (285,'new_year2','new_year2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (286,'new_year3','new_year3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (287,'new_year4','new_year4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (288,'new_year5','new_year5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (289,'new_year6','new_year6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (290,'new_year7','new_year7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (291,'new_year8','new_year8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (292,'new_year9','new_year9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (293,'saint1','saint1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (294,'saint10','saint10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (295,'saint11','saint11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (296,'saint12','saint12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (297,'saint13','saint13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (298,'saint14','saint14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (299,'saint15','saint15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (300,'saint16','saint16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (301,'saint17','saint17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (302,'saint18','saint18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (303,'saint19','saint19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (304,'saint2','saint2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (305,'saint20','saint20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (306,'saint21','saint21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (307,'saint22','saint22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (308,'saint23','saint23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (309,'saint24','saint24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (310,'saint25','saint25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (311,'saint26','saint26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (312,'saint27','saint27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (313,'saint28','saint28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (314,'saint29','saint29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (315,'saint3','saint3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (316,'saint30','saint30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (317,'saint31','saint31.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (318,'saint4','saint4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (319,'saint5','saint5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (320,'saint6','saint6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (321,'saint7','saint7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (322,'saint8','saint8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (323,'saint9','saint9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (324,'thanksgiving1','thanksgiving1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (325,'thanksgiving10','thanksgiving10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (326,'thanksgiving11','thanksgiving11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (327,'thanksgiving12','thanksgiving12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (328,'thanksgiving13','thanksgiving13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (329,'thanksgiving14','thanksgiving14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (330,'thanksgiving15','thanksgiving15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (331,'thanksgiving16','thanksgiving16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (332,'thanksgiving17','thanksgiving17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (333,'thanksgiving18','thanksgiving18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (334,'thanksgiving19','thanksgiving19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (335,'thanksgiving2','thanksgiving2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (336,'thanksgiving20','thanksgiving20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (337,'thanksgiving21','thanksgiving21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (338,'thanksgiving22','thanksgiving22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (339,'thanksgiving23','thanksgiving23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (340,'thanksgiving24','thanksgiving24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (341,'thanksgiving25','thanksgiving25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (342,'thanksgiving26','thanksgiving26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (343,'thanksgiving27','thanksgiving27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (344,'thanksgiving28','thanksgiving28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (345,'thanksgiving29','thanksgiving29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (346,'thanksgiving3','thanksgiving3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (347,'thanksgiving30','thanksgiving30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (348,'thanksgiving4','thanksgiving4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (349,'thanksgiving5','thanksgiving5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (350,'thanksgiving6','thanksgiving6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (351,'thanksgiving7','thanksgiving7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (352,'thanksgiving8','thanksgiving8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (353,'thanksgiving9','thanksgiving9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (354,'valentinesDay1','valentinesDay1.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (355,'valentinesDay10','valentinesDay10.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (356,'valentinesDay11','valentinesDay11.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (357,'valentinesDay12','valentinesDay12.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (358,'valentinesDay13','valentinesDay13.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (359,'valentinesDay14','valentinesDay14.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (360,'valentinesDay15','valentinesDay15.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (361,'valentinesDay16','valentinesDay16.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (362,'valentinesDay17','valentinesDay17.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (363,'valentinesDay18','valentinesDay18.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (364,'valentinesDay19','valentinesDay19.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (365,'valentinesDay2','valentinesDay2.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (366,'valentinesDay20','valentinesDay20.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (367,'valentinesDay21','valentinesDay21.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (368,'valentinesDay22','valentinesDay22.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (369,'valentinesDay23','valentinesDay23.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (370,'valentinesDay24','valentinesDay24.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (371,'valentinesDay25','valentinesDay25.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (372,'valentinesDay26','valentinesDay26.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (373,'valentinesDay27','valentinesDay27.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (374,'valentinesDay28','valentinesDay28.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (375,'valentinesDay29','valentinesDay29.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (376,'valentinesDay3','valentinesDay3.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (377,'valentinesDay30','valentinesDay30.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (378,'valentinesDay31','valentinesDay31.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (379,'valentinesDay32','valentinesDay32.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (380,'valentinesDay33','valentinesDay33.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (381,'valentinesDay34','valentinesDay34.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (382,'valentinesDay35','valentinesDay35.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (383,'valentinesDay36','valentinesDay36.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (384,'valentinesDay37','valentinesDay37.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (385,'valentinesDay38','valentinesDay38.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (386,'valentinesDay39','valentinesDay39.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (387,'valentinesDay4','valentinesDay4.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (388,'valentinesDay40','valentinesDay40.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (389,'valentinesDay5','valentinesDay5.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (390,'valentinesDay6','valentinesDay6.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (391,'valentinesDay7','valentinesDay7.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (392,'valentinesDay8','valentinesDay8.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (393,'valentinesDay9','valentinesDay9.png','',1,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (394,'Frame','template.jpg','template.jpg',3,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (395,'Halloween 1','halloween1_1.png','halloween1_1.png',3,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (396,'halloween2','halloween2_1.png','halloween2_1.png',3,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (397,'halloween3','halloween3_1.png','halloween3_1.png',3,1,0,'');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (398,'Video Halloween',NULL,'http://img.youtube.com/vi/Y4lgA6jY4xM/hqdefault.jpg',2,1,0,'https://www.youtube.com/watch?v=Y4lgA6jY4xM');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (399,'Video Halloween',NULL,'http://img.youtube.com/vi/PU9UCnqcLCE/hqdefault.jpg',2,1,0,'https://www.youtube.com/watch?v=PU9UCnqcLCE');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (400,'Video Halloween',NULL,'http://img.youtube.com/vi/Pdp-wIiRxXI/hqdefault.jpg',2,1,0,'https://www.youtube.com/watch?v=Pdp-wIiRxXI');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (401,'Video Mother\'s Day',NULL,'http://img.youtube.com/vi/GHvjTLOtq0U/hqdefault.jpg',2,1,0,'https://www.youtube.com/watch?v=GHvjTLOtq0U');
            insert  into {$this->getTable('giftcardtemplate/design_items')}(`item_id`,`name`,`source_file`,`thumb_file`,`format_id`,`status`,`is_default`,`video_url`) values (402,'Video Mother\'s Day',NULL,'http://img.youtube.com/vi/j2zhVs1cUgU/hqdefault.jpg',2,1,0,'https://www.youtube.com/watch?v=j2zhVs1cUgU');

        ");
        $setup->run("
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (68,1,163,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (69,1,164,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (70,1,165,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (71,1,166,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (72,1,167,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (73,1,168,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (74,1,169,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (75,1,170,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (76,1,171,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (77,1,172,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (78,1,173,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (79,1,174,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (80,1,175,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (81,1,176,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (82,1,177,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (83,1,178,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (84,1,179,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (85,1,180,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (86,1,181,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (87,1,182,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (88,2,277,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (89,2,278,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (90,2,279,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (91,2,280,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (92,2,281,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (93,2,282,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (94,2,283,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (95,2,284,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (96,2,285,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (97,2,286,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (98,2,287,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (99,2,288,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (100,2,289,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (101,2,290,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (102,2,291,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (103,2,292,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (104,3,63,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (105,3,64,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (106,3,65,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (107,3,66,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (108,3,67,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (109,3,68,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (110,3,69,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (111,3,70,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (112,3,71,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (113,3,72,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (114,3,73,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (115,3,74,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (116,3,75,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (117,3,76,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (118,3,77,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (119,3,78,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (120,3,79,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (121,3,80,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (122,3,81,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (123,3,82,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (124,4,93,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (125,4,94,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (126,4,95,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (127,4,96,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (128,4,97,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (129,4,98,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (130,4,99,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (131,4,100,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (132,4,101,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (133,4,102,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (134,4,103,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (135,4,104,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (136,4,105,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (137,4,106,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (158,7,183,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (159,7,184,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (160,7,185,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (161,7,186,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (162,7,187,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (163,7,188,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (164,7,189,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (165,7,190,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (166,7,191,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (167,7,192,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (168,7,193,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (169,7,194,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (170,7,195,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (171,7,196,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (172,7,197,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (173,7,198,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (174,7,199,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (175,7,200,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (176,7,201,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (177,7,202,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (198,10,137,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (199,10,138,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (200,10,139,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (201,10,140,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (202,10,141,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (203,10,142,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (204,10,143,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (205,10,144,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (206,10,145,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (207,10,146,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (208,10,147,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (209,10,148,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (210,10,149,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (211,10,150,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (212,10,151,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (213,10,152,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (214,10,153,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (215,10,154,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (216,10,155,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (217,10,156,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (218,11,247,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (219,11,248,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (220,11,249,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (221,11,250,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (222,11,251,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (223,11,252,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (224,11,253,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (225,11,254,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (226,11,255,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (227,11,256,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (228,11,257,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (229,11,258,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (230,11,259,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (231,11,260,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (232,11,261,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (233,11,262,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (234,11,263,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (235,11,264,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (236,11,265,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (237,11,266,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (238,12,293,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (239,12,294,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (240,12,295,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (241,12,296,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (242,12,297,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (243,12,298,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (244,12,299,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (245,12,300,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (246,12,301,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (247,12,302,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (248,12,303,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (249,12,304,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (250,12,305,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (251,12,306,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (252,12,307,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (253,12,308,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (254,12,309,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (255,12,310,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (256,12,311,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (257,12,312,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (258,13,324,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (259,13,325,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (260,13,326,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (261,13,327,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (262,13,328,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (263,13,329,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (264,13,330,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (265,13,331,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (266,13,332,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (267,13,333,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (268,13,334,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (269,13,335,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (270,13,336,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (271,13,337,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (272,13,338,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (273,13,339,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (274,13,340,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (275,13,341,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (276,13,342,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (277,13,343,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (278,14,354,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (279,14,355,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (280,14,356,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (281,14,357,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (282,14,358,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (283,14,359,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (284,14,360,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (285,14,361,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (286,14,362,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (287,14,363,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (288,14,364,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (289,14,365,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (290,14,366,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (291,14,367,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (292,14,368,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (293,14,369,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (294,14,370,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (295,14,371,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (296,14,372,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (297,14,373,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (319,9,216,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (320,9,217,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (321,9,218,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (322,9,219,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (323,9,220,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (324,9,221,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (325,9,222,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (326,9,223,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (327,9,224,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (328,9,225,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (329,9,226,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (330,9,227,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (331,9,228,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (332,9,229,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (333,9,230,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (334,9,231,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (335,9,232,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (336,9,233,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (337,9,234,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (338,9,235,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (339,9,236,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (340,9,237,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (341,9,238,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (342,9,239,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (343,9,240,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (344,9,241,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (345,9,242,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (346,9,243,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (347,9,244,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (348,9,245,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (349,9,246,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (350,1,394,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (351,9,395,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (352,9,396,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (353,9,397,NULL);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (354,9,398,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (355,9,399,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (356,9,400,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (359,11,401,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (360,11,402,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (786,6,107,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (787,6,108,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (788,6,109,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (789,6,110,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (790,6,111,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (791,6,112,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (792,6,113,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (793,6,114,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (794,6,115,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (795,6,116,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (796,6,117,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (797,6,118,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (798,6,119,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (799,6,120,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (800,6,121,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (801,6,122,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (802,6,123,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (803,6,124,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (804,6,125,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (805,6,126,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (806,6,127,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (807,6,128,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (808,6,129,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (809,6,130,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (810,6,131,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (811,6,132,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (812,6,133,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (813,6,134,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (814,6,135,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (815,6,136,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (816,6,277,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (817,6,278,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (818,6,279,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (819,6,280,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (820,6,281,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (821,6,282,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (822,6,283,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (823,6,284,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (824,6,285,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (825,6,286,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (826,6,287,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (827,6,288,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (828,6,289,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (829,6,290,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (830,6,291,0);
            insert  into {$this->getTable('giftcardtemplate/design_items_detail')}(`detail_id`,`design_id`,`item_id`,`sort_order`) values (831,6,292,0);

        ");
    }
}
