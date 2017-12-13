<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_View_Items
    extends ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_View_Abstract
{
    const DOCUMENT_TYPE_PACKING_LIST = 'packing_list';
    const DOCUMENT_TYPE_INVOICE = 'invoice';
    /**
     * Returns the document title.
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        if ($this->isPackingList()) {
            return $this->getDataSetDefault('document_title', Mage::helper('os_malaysia_invoice')->__('Packing List'));
        }

        return $this->getDataSetDefault('document_title', Mage::helper('os_malaysia_invoice')->__('Invoice'));
    }

    /**
     * Gets the document type.
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->getDataSetDefault('document_type', self::DOCUMENT_TYPE_INVOICE);
    }

    /**
     * Checks if the document is a packing list.
     *
     * @return bool
     */
    public function isPackingList()
    {
        return $this->getDocumentType() == self::DOCUMENT_TYPE_PACKING_LIST;
    }

    /**
     * Checks whether the document type is an invoice.
     *
     * @return bool
     */
    public function isInvoice()
    {
        return $this->getDocumentType() == self::DOCUMENT_TYPE_INVOICE;
    }

    /**
     * Formats the price in a given currency.
     *
     * @param $price
     * @param $currency
     * @return float|string
     */
    public function formatPrice($price, $currency)
    {
        $data = floatval($price);
        $data = sprintf("%f", $data);
        $data = Mage::app()->getLocale()->currency($currency)->toCurrency($data);
        return $data;
    }
}
