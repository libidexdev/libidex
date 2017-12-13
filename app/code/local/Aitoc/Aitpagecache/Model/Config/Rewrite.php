<?php
class Aitoc_Aitpagecache_Model_Config_Rewrite extends Mage_Core_Model_Config_Data
{
	protected function _afterSave()
	{
        parent::_afterSave();
        Mage::dispatchEvent('aitpagecache_config_changed', array('field'=> $this->getField(), 'value'=>$this->getValue()));
        Mage::helper('aitpagecache')->clearCache();
        return $this;
    }
}