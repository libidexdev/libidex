<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 25/10/16
 * Time: 10:45
 */
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'OrderController.php';

class Lexel_DeliveryComment_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController {

    /**
     * Add order comment action
     * Overridden to add delivery comment to table in order to pre-populate delivery comment box
     */
    public function addCommentAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;
                $addToDelivery = isset($data['add_to_delivery_comment']) ? $data['add_to_delivery_comment'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                if($addToDelivery == 1) {
                    $deliveryComment = Mage::getModel('lexel_deliverycomment/deliveryComment');
                    $statusHistoryCollection = $order->getStatusHistoryCollection();
                    $statusHistoryItem = $statusHistoryCollection->getLastItem();
                    $deliveryComment->addStatusDeliveryComment($statusHistoryItem);
                }

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

}