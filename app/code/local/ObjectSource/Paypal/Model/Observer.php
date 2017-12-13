<?php

class ObjectSource_Paypal_Model_Observer {
    public function saveOrderComment($observer){


        $orderIds = $observer->getData('order_ids');
        $orderComment = Mage::getSingleton('core/session')->getOrderComment();
        if($orderComment){
            foreach($orderIds as $orderId){
                $order = Mage::getModel('sales/order')->load($orderId);
                $order->setData('onestepcheckout_customercomment',$orderComment);
                $order->setData('customer_comment',$orderComment);
                $order->save();

                $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
                $quote->setData('onestepcheckout_customercomment',$orderComment);
                $quote->setData('customer_comment',$orderComment);
                $quote->save();

            }

        }
        Mage::getSingleton('core/session')->unsOrderComment();


    }
}