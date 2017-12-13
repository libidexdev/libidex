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

class Magegiant_GiftCard_Block_Adminhtml_Giftcard extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_giftcard';
        $this->_blockGroup = 'giftcard';
        $this->_headerText = Mage::helper('giftcard')->__('Manage Gift Codes');
        $this->_addButtonLabel = Mage::helper('giftcard')->__('Add Code');

        parent::__construct();

		$this->_addButton('import', array(
			'label'     => Mage::helper('giftcard')->__('Import Codes'),
			'onclick'   => 'giftcardGridJsObject.doImport(\'' . $this->getUrl('*/*/import') . '\')',
			'class'     => 'add',
		));
    }
}