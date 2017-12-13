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
 * Magenotification Adminhtml Feedback Renderer Sentstatus Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Feedback_Renderer_Sentstatus 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render Grid Column
     */
    public function render(Varien_Object $row)
    {
        if ($row->getIsSent() == '1') {
            return '<span class="grid-severity-notice"><span>' . $this->__('Sent') . '</span></span>';
        } else {
            return '<span class="grid-severity-critical"><span>' . $this->__('Not Sent') . '</span></span>';
        }
    }

}
