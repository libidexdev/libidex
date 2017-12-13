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
 * Magenotification Adminhtml Feedback Renderer Status Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_Feedback_Renderer_Status 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render Grid Column
     */
    public function render(Varien_Object $row)
    {
        $statuses = Mage::helper('magenotification')->getFeedbackStatusList();
        switch ((int) $row->getStatus()) {
            case 1:
                $prefix = 'notice';
                break;
            case 2:
                $prefix = 'critical';
                break;
            case 3:
            default:
                $prefix = 'major';
        }
        return '<span class="grid-severity-' . $prefix . '"><span>' . $statuses[(int) $row->getStatus()] . 
            '</span></span>';
    }

}
