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
class Magegiant_GiftCardTemplate_Helper_Block extends Mage_Core_Helper_Abstract
{

    const FULL_HANDLE_NAME = 'giftcard_template_handle';
    const CONFIG_BLOCKS_NODE = 'global/giftcardtemplate/blocks';
    const CONFIG_SECTIONS_NODE = 'global/giftcardtemplate/sections';

    public function getBlocksNode()
    {
        $config = Mage::getConfig()->getNode(self::CONFIG_BLOCKS_NODE);
        if (!is_null($config))
            return $config;

        return null;
    }

    public function getSectionsNode()
    {
        $config = Mage::getConfig()->getNode(self::CONFIG_SECTIONS_NODE);
        if (!is_null($config))
            return $config;

        return null;
    }

    public function getBlockActions()
    {
        return (array)$this->getBlocksNode()->children();
    }

    public function getBlockSections()
    {
        $node     = (array)$this->getSectionsNode()->children();
        $sections = array();
        foreach ($node as $k => $v) {
            $sections[$k] = $v;
        }

        return $sections;
    }

    /**
     * @param null $handle
     * @param null $layout
     */
    public function getActionBlocks($actionName = null, $handleName = null, $layout = null)
    {
        if (!$actionName)
            $actionName = Mage::app()->getRequest()->getActionName();
        if (!$handleName)
            $handleName = self::FULL_HANDLE_NAME;
        if (!$layout)
            $layout = Mage::app()->getLayout();
        $this->_initUpdateLayout($layout, $handleName);
        $blockNode = (array)$this->getBlocksNode()->$actionName;
        $blocks    = array();
        foreach ($blockNode as $action => $blockName) {
            $block = $layout->getBlock($blockName);
            if ($block) {
                $blocks[$action] = $block->toHtml();
            }
        }

        return $blocks;
    }

    /**
     * Init layout from handel name
     *
     * @param $layout
     * @param $handleName
     */
    protected function _initUpdateLayout($layout, $handleName)
    {
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->addHandle('STORE_' . Mage::app()->getStore()->getCode());
        $package = Mage::getSingleton('core/design_package');
        $update->addHandle(
            'THEME_' . $package->getArea() . '_' . $package->getPackageName() . '_' . $package->getTheme('layout')
        );
        $update->addHandle(strtolower($handleName));
        Mage::dispatchEvent(
            'controller_action_layout_load_before',
            array('action' => Mage::app()->getFrontController()->getAction(), 'layout' => $layout)
        );
        $update->load();
        Mage::dispatchEvent(
            'controller_action_layout_load_after',
            array('action' => Mage::app()->getFrontController()->getAction(), 'layout' => $layout)
        );
        $this->_initLayoutMessages('checkout/session', $layout);
        $layout->generateXml();
        $layout->generateBlocks();

    }

    /**
     * Initializing layout messages by message storage(s), loading and adding messages to layout messages block
     *
     * @param string|array $messagesStorage
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function _initLayoutMessages($messagesStorage, $layout)
    {
        if (!is_array($messagesStorage)) {
            $messagesStorage = array($messagesStorage);
        }
        foreach ($messagesStorage as $storageName) {
            $storage = Mage::getSingleton($storageName);
            if ($storage) {
                $block = $layout->getMessagesBlock();
                $block->addMessages($storage->getMessages(true));
                $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
                $block->addStorageType($storageName);
            } else {
                Mage::throwException(
                    Mage::helper('core')->__('Invalid messages storage "%s" for layout messages initialization', (string)$storageName)
                );
            }
        }

        return $this;
    }
}