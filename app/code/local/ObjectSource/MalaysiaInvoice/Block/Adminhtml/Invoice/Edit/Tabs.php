<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('malaysiainvoice_info_tabs');
        $this->setDestElementId('os_malaysia_invoice_edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Invoice Information'));
    }

    protected function _prepareLayout()
    {
        $invoice = $this->getInvoice();

        if (!($invoiceReference = $invoice->getInvoiceReference())) {
            $invoiceReference = $this->getRequest()->getParam('invoice_reference', null);
        }

        if (!($usdValue = $invoice->getMalaysiaTotalUsd())) {
            $usdValue = $this->getRequest()->getParam('malaysia_total_usd');
        }

        $this->addTab('settings', array(
            'label'     => Mage::helper('os_malaysia_invoice')->__('Invoice Settings'),
            'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('os_malaysia_invoice_adminhtml/invoice_edit_tab_settings')->toHtml()),
            'active'    => true,
        ));

        $this->addTab('invoice_items', array(
            'label'     => Mage::helper('os_malaysia_invoice')->__('Invoice Items'),
            'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('os_malaysia_invoice_adminhtml/invoice_edit_tab_items')->toHtml()),
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrive Malaysia Invoice
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getInvoice()
    {
        if (!($this->getData('invoice') instanceof ObjectSource_MalaysiaInvoice_Model_Invoice)) {
            $this->setData('invoice', Mage::registry('invoice'));
        }
        return $this->getData('invoice');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}