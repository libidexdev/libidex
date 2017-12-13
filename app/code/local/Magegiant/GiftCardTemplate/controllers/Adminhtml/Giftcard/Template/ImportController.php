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
 * @package     Magegiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardtemplate Adminhtml Controller
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Adminhtml_Giftcard_Template_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function sampleAction()
    {
        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = $this->getUrl("adminhtml/system_config/edit/section/giftcard/");
        }
        try {
            Mage::getResourceModel('giftcardtemplate/setup')->insertDefaultData();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Can not import data'));
        }
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Import data successfully'));
        $this->getResponse()->setRedirect($refererUrl);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftcard/giftcardtemplate/');
    }
}