<?php
class ObjectSource_ProductPageStep_Block_Adminhtml_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_attribute';
        $this->_headerText = Mage::helper('productpagestep')->__('Manage Attributes');
        $this->_blockGroup = 'productpagestep';
        //$this->_addButtonLabel = Mage::helper('productpagestep')->__('Load');
        parent::__construct();
    }
}