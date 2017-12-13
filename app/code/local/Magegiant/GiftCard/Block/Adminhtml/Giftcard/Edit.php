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

class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	const PAGE_TABS_BLOCK_ID = 'giftcard_tabs';

	public function __construct()
	{
		parent::__construct();

		$this->_objectId   = 'id';
		$this->_blockGroup = 'giftcard';
		$this->_controller = 'adminhtml_giftcard';

		$this->_addButton('saveandsendmail', array(
			'label'   => Mage::helper('adminhtml')->__('Save And Send Email'),
			'onclick' => 'editForm.submit(\'' . $this->_getSaveAndSendUrl() . '\')',
			'class'   => 'save',
		), -100);

		$this->_addButton('saveandcontinue', array(
			'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
			'class'   => 'save',
		), -100);

		$this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('giftcard_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'giftcard_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'giftcard_content');
            }

            function saveAndContinueEdit(urlTemplate){
                var urlTemplateSyntax = /(^|.|\\r|\\n)({{(\\w+)}})/;
                var template = new Template(urlTemplate, urlTemplateSyntax);
                var url = template.evaluate({tab_id:" . self::PAGE_TABS_BLOCK_ID . "JsTabs.activeTab.id});
                editForm.submit(url);
            }
        ";
	}

	protected function _getSaveAndContinueUrl()
	{
		return $this->getUrl('*/*/save', array(
			'_current'   => true,
			'back'       => 'edit',
			'tab'        => '{{tab_id}}',
			'active_tab' => null
		));
	}

	protected function _getSaveAndSendUrl()
	{
		return $this->getUrl('*/*/save', array(
			'_current' => true,
			'back'     => 'edit',
			'action'   => 'send',
		));
	}

	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		if (Mage::registry('current_giftcard') && Mage::registry('current_giftcard')->getId()) {
			return Mage::helper('giftcard')->__("Edit Gift Card: %s", $this->escapeHtml(Mage::registry('current_giftcard')->getCode()));
		}

		return Mage::helper('giftcard')->__('Add Gift Card');
	}
}