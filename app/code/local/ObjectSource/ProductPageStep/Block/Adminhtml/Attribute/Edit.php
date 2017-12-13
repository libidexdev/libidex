<?php
class ObjectSource_ProductPageStep_Block_Adminhtml_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'productpagestep';
        $this->_controller = 'adminhtml_attribute';
        
        parent::__construct();
    }

    public function getHeaderText()
    {
        /*
        $model = Mage::registry('amshopby_filter');
        
        if ($model) {
            $attribute =  Mage::getModel('eav/entity_attribute')->load($model->getAttributeId());
            return Mage::helper('amshopby')->__('Edit Filter "' . $attribute->getFrontendLabel() . '" Properties');
        }
        */
    }
}