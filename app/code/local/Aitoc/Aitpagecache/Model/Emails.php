<?php
class Aitoc_Aitpagecache_Model_Emails extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitpagecache/emails');
    }
}