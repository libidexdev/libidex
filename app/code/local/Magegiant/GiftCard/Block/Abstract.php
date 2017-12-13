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

class Magegiant_GiftCard_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magegiant_GiftCard_Block_Giftcard
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

	public function isEnable(){
		return $this->helper()->isEnabled();
	}

	public function helper($name = null)
	{
		if (is_null($name)) {
			return Mage::helper('giftcard');
		}

		return parent::helper($name);
	}

	public function getStatusLabel($card)
	{
		$statuses = Mage::getModel('giftcard/giftcard')->getStatusArray();

		if (isset($statuses[$card->getStatus()])) {
			$statusLabel = $statuses[$card->getStatus()];

			return $statusLabel;
		}

		return '';
	}
}