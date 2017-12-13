<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Productiondates extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_productiondates';
        $this->_headerText = Mage::helper('orderfulfillment')->__('Manage Production Dates');
        $this->_blockGroup = 'orderfulfillment';
        //$this->_addButtonLabel = Mage::helper('productpagestep')->__('Load');
        parent::__construct();
    }
}