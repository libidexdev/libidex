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
class Magegiant_GiftCard_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare form's information for block
	 *
	 * @return Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Form
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'      => 'edit_form',
			'action'  => $this->getUrl('*/*/importPost'),
			'method'  => 'post',
			'enctype' => 'multipart/form-data'
		));

		$fieldset = $form->addFieldset('import_form', array(
			'legend' => Mage::helper('giftcard')->__('Import Form')
		));

		$fieldset->addField('import_file', 'file', array(
			'name'  => 'import_file',
			'label' => Mage::helper('giftcard')->__('Import File'),
			'title' => Mage::helper('giftcard')->__('Import File'),
			'note'  => '.csv is allowed.'
		));

		$fieldset->addField('import_file_example', 'link', array(
			'label'  => Mage::helper('giftcard')->__('Sample .csv file'),
			'href'   => $this->getUrl('*/*/importSample'),
			'value'  => 'example.csv',
			'target' => '_blank',
			'style'  => 'text-decoration: none'
		));


		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}