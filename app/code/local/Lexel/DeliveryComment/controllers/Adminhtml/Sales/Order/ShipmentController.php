<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 27/10/16
 * Time: 14:56
 */
require_once Mage::getModuleDir('controllers', 'Webshopapps_Dropship') . DS . 'Adminhtml' . DS . 'Sales' . DS . 'ShipmentController.php';

if (Mage::helper('core')->isModuleEnabled('Webshopapps_Dropship') && class_exists('Webshopapps_Dropship_Adminhtml_Sales_ShipmentController')) {
    abstract class Lexel_DeliveryComment_Adminhtml_Sales_Order_ShipmentController_Abstract extends Webshopapps_Dropship_Adminhtml_Sales_ShipmentController {}
    } else {
    abstract class Lexel_DeliveryComment_Adminhtml_Sales_Order_ShipmentController_Abstract extends Mage_Adminhtml_Sales_Order_ShipmentController {}
}

class Lexel_DeliveryComment_Adminhtml_Sales_Order_ShipmentController extends Lexel_DeliveryComment_Adminhtml_Sales_Order_ShipmentController_Abstract {

//class Lexel_DeliveryComment_Adminhtml_Sales_Order_ShipmentController extends Webshopapps_Dropship_Adminhtml_Sales_ShipmentController {

    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam(
                'shipment_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field cannot be empty.'));
            }
            $shipment = $this->_initShipment();
            $shipment->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $shipment->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();

            // Lexel update lexel_delivery_comments table to ensure comments are marked as sent
            $deliveryComment = Mage::getModel('lexel_deliverycomment/deliveryComment');
            $deliveryComment->markCommentAsSent($data['processed_delivery_comment']);

            $this->loadLayout(false);
            $response = $this->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }
}