<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * GiftCardTemplate Helper
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardTemplate_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'giftcard/giftcardtemplate/is_enabled';
    const XML_PATH_GENERAL_CONFIG = 'giftcard/giftcardtemplate/';

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED, $storeId);
    }

    /**
     * @param      $name
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($name, $storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_CONFIG . $name, $storeId);

    }

    public function getMaxFileSize($store = null)
    {
        return $this->getGeneralConfig('max_file_size', $store);
    }

    public function getUploadLabel($store = null)
    {
        return $this->getGeneralConfig('upload_label', $store);
    }

}