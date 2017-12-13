<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_View_Abstract extends Mage_Core_Block_Template
{
    /**
     * Gets the current invoice.
     *
     * @return ObjectSource_MalaysiaInvoice_Model_Invoice
     */
    public function getInvoice()
    {
       return Mage::registry('current_invoice');
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/company_name');
    }

    /**
     * @return string
     */
    public function getCompanyRegistrationText()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/company_registration_text');
    }

    /**
     * @return string
     */
    public function getCompanyAddress()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/company_address');
    }

    /**
     * @return string
     */
    public function getTelephoneNumber()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/company_telephone_number');
    }

    /**
     * @return string
     */
    public function getFaxNumber()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/company_fax_number');
    }

    /**
     * @return string
     */
    public function getGstRegistrationText()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/gst_registration_text');
    }

    public function getLibidexAddressHtml()
    {
        return nl2br(Mage::getStoreConfig('general/malaysia_invoice/libidex_address'));
    }

    /**
     * Returns Libidex bank details
     *
     * @return string
     */
    public function getLibidexBankDetails()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/libidex_bank_detail');
    }

    public function getOperationManagerName()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/operation_manager_name');
    }

    public function getOperationManagerPosition()
    {
        return Mage::getStoreConfig('general/malaysia_invoice/operation_manager_position');
    }
}
