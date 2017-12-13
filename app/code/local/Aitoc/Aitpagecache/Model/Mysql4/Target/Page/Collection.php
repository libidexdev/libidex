<?php

class Aitoc_Aitpagecache_Model_Mysql4_Target_Page_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitpagecache/target_page');
    }
}