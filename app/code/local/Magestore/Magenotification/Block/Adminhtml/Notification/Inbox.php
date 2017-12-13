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
 * Magenotification Adminhtml Notification Inbox Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Notification_Inbox extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_notification_inbox';
        $this->_blockGroup = 'magenotification';
        $this->_headerText = Mage::helper('magenotification')->__('Messages Inbox');
        parent::__construct();
    }

    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return parent::_prepareLayout();
    }

}
