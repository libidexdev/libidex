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

class Magegiant_GiftCard_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_objectId   = 'id';
		$this->_blockGroup = 'giftcard';
		$this->_controller = 'adminhtml_import';

		$this->removeButton('back');
		$this->removeButton('reset');
		$this->removeButton('delete');

        $this->_updateButton('save', 'label', Mage::helper('giftcard')->__('Import'));

		$this->_addButton('close', array(
			'label'   => Mage::helper('adminhtml')->__('Close Window'),
			'onclick' => 'window.close()',
			'class'   => 'delete',
		), 0);
	}

	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		return Mage::helper('giftcard')->__('Import Gift Card');
	}
}