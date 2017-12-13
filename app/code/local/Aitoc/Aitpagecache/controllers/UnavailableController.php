<?php
class Aitoc_Aitpagecache_UnavailableController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
        $blockId = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_block');
        if($blockId)
        {
            $block = $this->getLayout()->createBlock('cms/block')->setBlockId($blockId);
            $this->getLayout()->getBlock('content')->append($block);
        }
        $emailBlock = $this->getLayout()->createBlock('core/template')->setTemplate('aitpagecache/emailblock.phtml');
        $this->getLayout()->getBlock('content')->append($emailBlock);
        $this->renderLayout();
    }

    public function returnAction()
    {
        Mage::getSingleton('core/session')->setCustomerIsReturning(true);
        header('Location: '.Mage::getBaseUrl());
        exit;
    }
}