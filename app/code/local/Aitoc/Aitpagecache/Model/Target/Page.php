<?php

class Aitoc_Aitpagecache_Model_Target_Page extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('aitpagecache/target_page');
    }
}