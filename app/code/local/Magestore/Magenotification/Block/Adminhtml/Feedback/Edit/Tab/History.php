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
 * Magenotification Adminhtml Feedback Edit Tab History Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tab_History extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('magenotification/feedback/history.phtml');
    }

    public function getFeedback()
    {
        return Mage::registry('feedback_data');
    }

    public function getMessages()
    {
        return $this->getFeedback()->getMessages();
    }

    public function getMessageTitle($message)
    {
        $title = '<b>';
        $title .= ($message->getIsCustomer() == '1') ? $this->__('From Admin') : 
            $this->__('From') . ' ' . Magestore_Magenotification_Model_Keygen::SERVER_NAME;
        $title .= ' - ' . $message->getUser() . '</b> ';
        $title .= $this->__('on') . ' ';
        $title .= Mage::helper('core')->formatDate($message->getPostedTime(), 'medium', true);
        return $title;
    }

}
