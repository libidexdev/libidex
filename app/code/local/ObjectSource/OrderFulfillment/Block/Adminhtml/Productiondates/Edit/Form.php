<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Productiondates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form', 
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $hlp = Mage::helper('orderfulfillment');
        $model = Mage::registry('orderfulfillment_productiondates');

        $fldAssoc = $form->addFieldset('assoc', array('legend'=> $hlp->__('Processing Dates')));
        $fldAssoc->addField('label', 'text', array(
            'label'     => $hlp->__('Description'),
            'name'      => 'label',
            'note' => '<small>Label to describe production date range</small>',
        ));
        $fldAssoc->addField('processing_total_from', 'text', array(
            'label'     => $hlp->__('Processing Total From'),
            'name'      => 'processing_total_from',
            'note' => '<small>Minimum orders processing for range</small>',
        ));
        $fldAssoc->addField('processing_total_to', 'text', array(
            'label'     => $hlp->__('Processing Total To'),
            'name'      => 'processing_total_to',
            'note' => '<small>Maximum orders processing for range</small>',
        ));
        $fldAssoc->addField('delayed', 'text', array(
            'label'     => $hlp->__('Delayed (in days)'),
            'name'      => 'delayed',
            'note' => '<small>Number of days to delay finish date</small>',
        ));

        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
}