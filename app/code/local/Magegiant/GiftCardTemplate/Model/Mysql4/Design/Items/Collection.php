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

/**
 * Giftcardtemplate Resource Collection Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardTemplate_Model_Mysql4_Design_Items_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('giftcardtemplate/design_items');
    }

    /**
     * @param $status
     * @return $this
     */
    public function addStatusToFilter($status)
    {
        $this->getSelect()->where('main_table.status=?', $status);

        return $this;
    }

    public function getAvailableItems()
    {
        $this->addStatusToFilter(Magegiant_GiftCardTemplate_Model_Status::STATUS_ENABLED);

        return $this;

    }

    /**
     * @param $design
     * @return $this
     */
    public function getAllItemsByDesignIds($designIds = array(), $group = null, $website = null)
    {
        $this->addDesignToFilter($designIds);
        $this
            ->addFieldToFilter('design.customer_group_ids', array(
                'finset' => array($group)
            ))
            ->addFieldToFilter('design.website_ids', array(
                'finset' => array($website)
            ));
        $this->getSelect()->order('detail.sort_order DESC');

        return $this;
    }

    public function addDesignToFilter($designIds)
    {
        $this->getSelect()->join(array('detail' => $this->getTable('giftcardtemplate/design_items_detail')),
            'main_table.item_id=detail.item_id',
            array('*'))
            ->join(array('design' => $this->getTable('giftcardtemplate/design')),
                'detail.design_id=design.design_id',
                array('*'));
        $this->_setIdFieldName('detail_id');
        if (!empty($designIds))
            $this->getSelect()->where('detail.design_id in(?)', $designIds);

        return $this;
    }

    /**
     * @param $design
     * @return $this
     */
    public function addFormatToFilter($format = null)
    {
        if ($format) {
            $this->getSelect()->where('main_table.format_id=?', $format);
        }

        return $this;
    }

    public function getAllFormatsByDesignIds($designIds)
    {
        $this->addDesignToFilter($designIds);
        $this->getSelect()->group('format_id');

        return $this;
    }
}