<?php
class ObjectSOurce_MalaysiaInvoice_Model_Resource_Invoice_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('os_malaysia_invoice/invoice');
    }

    public function joinItems()
    {
        //$this->join('sales/order_item', '')
    }
}
