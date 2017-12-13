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
 * Magenotification Adminhtml Feedback Edit Tabs Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('feedback_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('magenotification')->__('Feedback Detail'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('magenotification')->__('Feedback Detail'),
            'title' => Mage::helper('magenotification')->__('Feedback Detail'),
            'content' => $this->getLayout()
                ->createBlock('magenotification/adminhtml_feedback_edit_tab_form')->toHtml(),
        ));

        if ($this->getRequest()->getParam('id')) {
            $this->addTab('message_section', array(
                'label' => Mage::helper('magenotification')->__('Post Message'),
                'title' => Mage::helper('magenotification')->__('Post Message'),
                'content' => $this->getLayout()
                    ->createBlock('magenotification/adminhtml_feedback_edit_tab_message')->toHtml(),
            ));

            $this->addTab('history_section', array(
                'label' => Mage::helper('magenotification')->__('View Posted Message'),
                'title' => Mage::helper('magenotification')->__('View Posted Message'),
                'content' => $this->getLayout()
                    ->createBlock('magenotification/adminhtml_feedback_edit_tab_history')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

}
