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
class Magegiant_GiftCardTemplate_Model_Mysql4_Design_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('giftcardtemplate/design');
    }

    public function addStatusToFilter($status)
    {
        $this->addFieldToFilter('main_table.status', $status);

        return $this;
    }

    public function addGroupToFilter($group)
    {
        $this->addFieldToFilter('customer_group_ids', array(
            'finset' => array($group)
        ));

        return $this;
    }

    public function addWebsiteToFilter($website)
    {
        $this->addFieldToFilter('website_ids', array(
            'finset' => array($website)
        ));

        return $this;
    }

    public function addFormatToFilter($format)
    {
        $this->getSelect()->join(array('detail' => $this->getTable('giftcardtemplate/design_items_detail')),
            'main_table.design_id=detail.design_id',
            array('*'))
            ->join(array('items' => $this->getTable('giftcardtemplate/design_items')),
                'detail.item_id=items.item_id',
                array('items.format_id'))
            ->where('items.format_id =?', $format)
            ->group('main_table.design_id');

        return $this;
    }

}