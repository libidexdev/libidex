<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Magenotification
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Magenotification Adminhtml Feedback Edit Tab Message Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tab_Message extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('feedback_message', array(
            'legend' => Mage::helper('magenotification')->__('Post Message')
        ));

        $fieldset->addField('message', 'editor', array(
            'name' => 'message',
            'label' => Mage::helper('magenotification')->__('Message'),
            'style' => 'width:600px;height:150px',
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('attached_file', 'note', array(
            'name' => 'attached_file',
            'label' => Mage::helper('magenotification')->__('Attached Files'),
            'text' => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_renderer_file')->toHtml(),
        ));


        return parent::_prepareForm();
    }

}
