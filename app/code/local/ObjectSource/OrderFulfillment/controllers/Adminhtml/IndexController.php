<?php
class ObjectSource_OrderFulfillment_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function jobTicketLoaderAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $approved = $order->getFulfillmentDataValue('approved');
            if ((Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleName() == 'Malaysia') && empty($approved)) {
                $error = 1;
            }
        }

        if (!is_array($orderIds))
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select purchase order(s).'));
            $this->_redirect('adminhtml/sales_order/index');
        }
        else if (!empty($error)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('One or more selected orders has not been approved for printing.'));
            $this->_redirect('adminhtml/sales_order/index');
        }
        else
        {
            $this->loadLayout();
            $this->getLayout()->getBlock('loader')->setOrderIds($orderIds);
            $this->renderLayout();
            //Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d purchase order(s) were prepared.', count($orderIds)));
        }
    }

    public function jobTicketPreviewAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        $orderIds = json_decode($orderIds);

        $this->loadLayout();
        $this->getLayout()->getBlock('preview')->setOrderIds($orderIds);
        $this->renderLayout();
    }

    public function approveAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $order->setFulfillmentDataValue('approved', 1);
        }
        Mage::getSingleton('orderfulfillment/observer')->updateGridWithFulfillmentData(null);
        $this->_redirect('adminhtml/sales_order/index');
    }
 
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('objectsource/orderfulfillment');
    }
}
