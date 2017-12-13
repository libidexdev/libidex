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
 * @package     Magegiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardcredit Adminhtml Block
 * 
 * @category    Magegiant
 * @package     Magegiant_GiftCardCredit
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardCredit_Block_Adminhtml_Credit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_credit';
        $this->_blockGroup = 'giftcardcredit';
        $this->_headerText = Mage::helper('giftcardcredit')->__('Gift Card Credit');

        parent::__construct();

		$this->removeButton('add');
    }
}