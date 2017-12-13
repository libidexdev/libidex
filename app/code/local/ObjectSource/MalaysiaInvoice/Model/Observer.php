<?php
class ObjectSource_MalaysiaInvoice_Model_Observer
{
    public function addMalaysiaInvoiceMassAction(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block->getRequest()->getControllerName() == 'sales_order' &&
            $block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
            )
        {
            $block->addItem('malaysia_invoice', array(
                'label' => Mage::helper('os_malaysia_invoice')->__('Add to Malaysia Invoice'),
                'url' => Mage::getUrl('adminhtml/malaysiainvoice/new'),
            ));
        }
    }
}
