<?php
class ObjectSource_OrderFulfillment_Adminhtml_Sales_OrdersController extends Mage_Adminhtml_Controller_Action
{
    public function saveOrderFullfillmentDateAction() {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        foreach (array('start_date', 'finish_date') as $key) {
            $value = $this->getRequest()->getParam($key);
            if (!empty($value)) {
                $order->setFulfillmentDataValue($key, $value);
            };
        }

        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$id)));
    }
}