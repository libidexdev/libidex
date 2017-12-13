<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Admin session
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    protected $_blockGroup = 'os_malaysia_invoice_adminhtml';

    public function __construct()
    {
        $this->_objectId    = 'invoice_id';
        $this->_controller  = 'invoice';
        $this->_mode        = 'view';
        $this->_session = Mage::getSingleton('admin/session');

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->getInvoice()->getId()) {
            $this->_addButton('print_pl', array(
                    'label'     => Mage::helper('os_malaysia_invoice')->__('Print Packing List'),
                    'class'     => 'save',
                    'onclick'   => 'openInNewTab(\''.$this->getPrintPackingListUrl().'\')'
                )
            );

            $this->_addButton('print_in', array(
                    'label'     => Mage::helper('os_malaysia_invoice')->__('Print Invoice'),
                    'class'     => 'save',
                    'onclick'   => 'openInNewTab(\''.$this->getPrintInvoiceUrl().'\')'
                )
            );

        }
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    public function getHeaderText()
    {
        return Mage::helper('os_malaysia_invoice')->__('Invoice #%d', $this->getInvoice()->getId());
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getPrintPackingListUrl()
    {
        return $this->getUrl('*/*/printPackingList', array(
            'invoice_id' => $this->getInvoice()->getId()
        ));
    }

    public function getPrintInvoiceUrl()
    {
        return $this->getUrl('*/*/printInvoice', array(
            'invoice_id' => $this->getInvoice()->getId()
        ));
    }
}
