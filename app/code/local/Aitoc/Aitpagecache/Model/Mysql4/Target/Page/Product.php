<?php

class Aitoc_Aitpagecache_Model_Mysql4_Target_Page_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('aitpagecache/target_page_product', null);
    }

    public function saveTargetData($productsToInsert)
    {
        $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $productsToInsert);
    }
}