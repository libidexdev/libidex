<?php
class Aitoc_Aitpagecache_Block_Js extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpagecache/js.phtml');
    }
    
    public function getDisabledCacheBlocks($toJson = true)
    {
    	return Mage::helper('aitpagecache')->getDisabledCacheBlocks($toJson);    	
    }    
}