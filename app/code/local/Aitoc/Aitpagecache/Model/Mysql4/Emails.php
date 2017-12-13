<?php
class Aitoc_Aitpagecache_Model_Mysql4_Emails extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aitpagecache/emails', 'email_id');
    }
}