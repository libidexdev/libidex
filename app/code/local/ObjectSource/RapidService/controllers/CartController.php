<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 02/09/16
 * Time: 16:17
 */
//require_once "Mage/Checkout/controllers/CartController.php";
class ObjectSource_RapidService_CartController extends Mage_Core_Controller_Front_Action {

    public function cartAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function addAction()
    {
        $args = Mage::app()->getRequest()->getPost();
        if($item = Mage::getModel('os_rapidservice/Cart')->addToQuote($args)) {
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($item));
        }
    }

    public function removeAction() {

        if($total = Mage::getModel('os_rapidservice/Cart')->removeFromQuote()) {
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($total));
        }
    }
}