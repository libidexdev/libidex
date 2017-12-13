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
 * GiftCardTemplate Index Controller
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_ProcessorController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function initPopupAction()
    {
        $result = array(
            'success'  => true,
            'messages' => array(),
            'blocks'   => array()
        );

        $result['blocks'] = $this->getBlockHelper()->getActionBlocks();

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }


    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function changeFormatAction()
    {
        $result = array(
            'success'  => true,
            'messages' => array(),
            'blocks'   => array()
        );

        $result['blocks'] = $this->getBlockHelper()->getActionBlocks();

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function changeDesignAction()
    {
        $result           = array(
            'success'  => true,
            'messages' => array(),
            'blocks'   => array()
        );
        $result['blocks'] = $this->getBlockHelper()->getActionBlocks();

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function changeModeAction()
    {
        $result           = array(
            'success'  => true,
            'messages' => array(),
            'blocks'   => array()
        );
        $result['blocks'] = $this->getBlockHelper()->getActionBlocks();

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function uploadImageAction()
    {
        $result          = array(
            'image' => ''
        );
        $uploadHelper    = Mage::helper('giftcardtemplate/upload');
        $result['image'] = $uploadHelper->uploadFile('file', 'upload');

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function embedVideoAction()
    {
        $result = array(
            'success' => false
        );
        if ($url = $this->getRequest()->getParam('video_url')) {
            $videoId = Mage::helper('giftcardtemplate')->getVideoIdByUrl($url);
            if ($videoId) {
                $isSecure = Mage::app()->getStore()->isCurrentlySecure();
                if ($isSecure) {
                    $embedLink = 'https://www.youtube.com/embed/';
                } else {
                    $embedLink = 'http://www.youtube.com/embed/';
                }
                $embedLink .= $videoId;
                $result['success']   = true;
                $result['embed_url'] = $embedLink;
            }
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function removeImageAction()
    {
        $result       = array(
            'success' => true
        );
        $uploadHelper = Mage::helper('giftcardtemplate/upload');
        if ($uploadedImg = $this->getRequest()->getParam('current_uploaded')) {
            try {
                $uploadHelper->deleteFile($uploadedImg, 'upload');
            } catch (Exception $e) {
                $result['success'] = false;
            }
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function printAction()
    {
        $card = Mage::getModel('giftcard/giftcard')->load($this->getRequest()->getParam('id'));
        if ($card && $card->getId()) {
            $block = $this->getLayout()->createBlock('core/template')
                ->setCard($card)
                ->setTemplate('magegiant/giftcardtemplate/print/designed.phtml');
            echo $block->renderView();
        }
    }

    public function changeFormFormatAction()
    {
        $result    = array(
            'success'  => true,
            'messages' => array(),
            'blocks'   => array()
        );
        $productId = $this->getRequest()->getParam('product_id');
        $product   = Mage::getModel('catalog/product')->load($productId);
        Mage::register('current_product', $product);
        $result['blocks'] = $this->getBlockHelper()->getActionBlocks();

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * @return Magegiant_GiftCardTemplate_Helper_Block
     */
    public function getBlockHelper()
    {
        return Mage::helper('giftcardtemplate/block');
    }
}