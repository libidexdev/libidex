<?php
class ObjectSource_ProductPageStep_Block_Adminhtml_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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

        $hlp = Mage::helper('productpagestep');
        $model = Mage::registry('productpagestep_attribute');

        $fldAssoc = $form->addFieldset('assoc', array('legend'=> $hlp->__('Association')));
        $fldAssoc->addField('category', 'text', array(
            'label'     => $hlp->__('Category'),
            'name'      => 'category',
            'note' => '<small>Category in which the option should be rendered. For example: COLOUR&gt;TRIM</small>',
        ));
        $fldAssoc->addField('option_label', 'text', array(
            'label'     => $hlp->__('Option Label'),
            'name'      => 'option_label',
            'note' => '<small>Label of the option as set in Magmi and product attributes (for configurables)</small>',
        ));
        $fldAssoc->addField('class', 'select', array(
            'label'     => $hlp->__('Class'),
            'name'      => 'class',
            'values'    => array(
                array(
                    'value' => 'swatch_color_main',
                    'label' => Mage::helper('catalog')->__('Color swatch with preview')
                ),
                array(
                    'value' => 'swatch_garment',
                    'label' => Mage::helper('catalog')->__('Garment Options swatch')
                ),
                array(
                    'value' => 'text_selection',
                    'label' => Mage::helper('catalog')->__('Text Options')
                ),
                array(
                    'value' => 'swatch_no_preview',
                    'label' => Mage::helper('catalog')->__('Swatch no preview')
                ),
            ),
            'note' => '<small>Design class to associate with option for frontend specific rendering</small>',
        ));

        $fldCont = $form->addFieldset('content', array('legend'=> $hlp->__('Contents')));
        $fldCont->addField('title1', 'text', array(
            'label'     => $hlp->__('Title 1'),
            'name'      => 'title1',
            'note' => '<small>Main title displayed for the option to be rendered on the frontend</small>',
        ));
        $fldCont->addField('title2', 'text', array(
            'label'     => $hlp->__('Title 2'),
            'name'      => 'title2',
            'note' => '<small>Sub title displayed for the option to be rendered on the frontend</small>',
        ));
        $fldCont->addField('content1', 'textarea', array(
            'label'     => $hlp->__('Content 1'),
            'name'      => 'content1',
            'note' => '<small>Main content displayed for the option to be rendered on the frontend</small>',
        ));
        $fldCont->addField('content2', 'textarea', array(
            'label'     => $hlp->__('Content 2'),
            'name'      => 'content2',
            'note' => '<small>Sub content displayed for the option to be rendered on the frontend</small>',
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