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
 * Magenotification Adminhtml Lisence Purchaseform Block
 *
 * @category Magestore
 * @package  Magestore_Magenotification
 * @module   Magenotification
 * @author   Magestore Developer
 */
class Magestore_Magenotification_Block_Adminhtml_License_Purchaseform extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('magenotification/license/purchaseform.phtml');
    }

    public function getPurchaseUrl()
    {
        return $this->getUrl('magenotification/adminhtml_license/purchase', array(
                'extension' => $this->getExtensionName(),
                '_secure' => true
        ));
    }

}
