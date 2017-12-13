<?php
class ObjectSource_MalaysiaInvoice_Model_Resource_Invoice extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('os_malaysia_invoice/invoice', 'entity_id');
    }
}
