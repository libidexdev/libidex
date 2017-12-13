<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('os_malaysia_invoice')->__('Invoice Settings')));

        $invoice = Mage::registry('invoice');
        //$entityType = Mage::registry('product')->getResource()->getEntityType();

        $fieldset->addField('invoice_reference', 'text', array(
            'label' => Mage::helper('os_malaysia_invoice')->__('Invoice Reference'),
            'title' => Mage::helper('os_malaysia_invoice')->__('Invoice Reference'),
            'name'  => 'invoice_reference',
            'value' => $invoice->getInvoiceReference(),
            'required'  => true,
        ));

        $fieldset->addField('awb_number', 'text', array(
            'label' => Mage::helper('os_malaysia_invoice')->__('Airway Bill Number'),
            'title' => Mage::helper('os_malaysia_invoice')->__('Airway Bill Number'),
            'name'  => 'awb_number',
            'value' => $invoice->getAwbNumber(),
            'required'  => true,
        ));

        $fieldset->addField('exchange_rate_gbp_usd', 'text', array(
            'label' => Mage::helper('os_malaysia_invoice')->__('Exchange Rate (GBP->USD)'),
            'title' => Mage::helper('os_malaysia_invoice')->__('Exchange Rate (GBP->USD)'),
            'name'  => 'exchange_rate_gbp_usd',
            'value' => $invoice->getExchangeRateGbpUsd(),
            'required'  => true,
        ));

        $fieldset->addField('malaysia_total_usd', 'text', array(
            'label' => Mage::helper('os_malaysia_invoice')->__('Total Value (USD)'),
            'title' => Mage::helper('os_malaysia_invoice')->__('Total Value (USD)'),
            'name'  => 'malaysia_total_usd',
            'value' => $invoice->getMalaysiaTotalUsd(),
            'required'  => false,
        ));



        $fieldset->addField('order_ids', 'hidden', array(
            'name'  => 'order_ids',
            'value' => implode(',', $invoice->getOrderIds()),
        ));

        $this->setForm($form);
    }
}
