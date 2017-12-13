<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Productiondates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'orderfulfillment';
        $this->_controller = 'adminhtml_productiondates';
        
        parent::__construct();
    }
}