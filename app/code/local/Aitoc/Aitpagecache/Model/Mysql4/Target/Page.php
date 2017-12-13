<?php

class Aitoc_Aitpagecache_Model_Mysql4_Target_Page extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('aitpagecache/target_page', 'page_id');
    }
}