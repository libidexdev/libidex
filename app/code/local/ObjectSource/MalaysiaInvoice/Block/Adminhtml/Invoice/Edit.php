<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_Edit
    extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('objectsource/malaysiainvoice/edit.phtml');
        $this->setId('invoice_edit');
    }

    public function getHeader() {
        return Mage::helper('os_malaysia_invoice')->__('Malaysia Invoice');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('os_malaysia_invoice')->__('Back'),
                    'onclick'   => 'setLocation(\''
                        . $this->getUrl('sales/orders/'),
                    'class' => 'back'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Save'),
                    'onclick'   => 'invoiceForm.submit()',
                    'class' => 'save'
                ))
        );

        return parent::_prepareLayout();
    }
}
