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
 * Magenotification Adminhtml Notification Inbox Grid Renderer Actions Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Notification_Inbox_Grid_Renderer_Actions
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $readDetailsHtml = ($row->getUrl()) ? '<a target="_blank" href="' . $row->getUrl() . '">' .
            Mage::helper('adminnotification')->__('Read Details') . '</a> | ' : '';

        $markAsReadHtml = (!$row->getIsRead()) ? '<a href="' . 
            $this->getUrl('*/*/markAsRead/', array('_current' => true, 'id' => $row->getId())) . '">' .
            Mage::helper('adminnotification')->__('Mark as Read') . '</a> | ' : '';

        return sprintf('%s%s<a href="%s" onClick="deleteConfirm(\'%s\', this.href); return false;">%s</a>', 
            $readDetailsHtml, $markAsReadHtml, $this->getUrl('*/*/remove/', array(
                '_current' => true,
                'id' => $row->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl())
            ), Mage::helper('adminnotification')->__('Are you sure?'), Mage::helper('adminnotification')->__('Remove')
        );
    }

}
