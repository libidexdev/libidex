<?php
class Amasty_Orderstatus_Model_Mysql4_Status extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderstatus/status', 'entity_id');
    }
}