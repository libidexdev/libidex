<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'os_malaysia_invoice_adminhtml';
        $this->_controller = 'invoice';
        $this->_headerText = Mage::helper('os_malaysia_invoice')->__('Malaysia Invoices');
        $this->removeButton('add');
    }
}
