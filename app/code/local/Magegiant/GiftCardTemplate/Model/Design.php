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
 * Giftcardtemplate Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardTemplate_Model_Design extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('giftcardtemplate/design');
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (is_array($this->getWebsiteIds())) {
            $this->setWebsiteIds(implode(',', $this->getWebsiteIds()));
        }
        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(implode(',', $this->getCustomerGroupIds()));
        }
        if (is_array($this->getFormatIds())) {
            $this->setFormatIds(implode(',', $this->getFormatIds()));
        }

        return parent::_beforeSave();
    }

    /**
     * get all design that have status 1
     *
     * @param $group
     * @param $website
     * @return mixed
     */
    public function getAllDesigns($group, $website, $ids = array())
    {
        $collection = $this->getCollection();
        $collection
            ->addStatusToFilter(Magegiant_GiftCardTemplate_Model_Status::STATUS_ENABLED)
            ->addGroupToFilter($group)
            ->addWebsiteToFilter($website);
        if (!empty($ids)) {
            $collection->addFieldToFilter('design_id', array('in' => $ids));
        }

        return $collection;

    }
}