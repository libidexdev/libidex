<?php
class ObjectSource_MalaysiaInvoice_Model_Invoice extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('os_malaysia_invoice/invoice');
    }

    /**
     * Gets collection of invoice items.
     *
     * @return ObjectSource_MalaysiaInvoice_Model_Resource_Invoice_Item_Collection
     */
    public function getItems()
    {
        return Mage::getResourceModel('os_malaysia_invoice/invoice_item_collection')
            ->addFieldToFilter('invoice_id', $this->getId());
    }
}
