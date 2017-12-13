<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Jobticket_Abstract extends Mage_Adminhtml_Block_Template
{
    protected $_orderIds = array();

    public function getOrders()
    {
        return Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $this->_orderIds));
    }

    public function setOrderIds($orderIds)
    {
        $this->_orderIds = $orderIds;
    }

    public function getOrderIds()
    {
        return $this->_orderIds;
    }
}