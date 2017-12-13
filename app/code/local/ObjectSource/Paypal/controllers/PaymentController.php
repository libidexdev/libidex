<?php
/**
 * Created by PhpStorm.
 * User: que
 * Date: 08/10/15
 * Time: 08:49
 */
require_once 'Ebizmarts/SagePaySuite/controllers/PaymentController.php';


class ObjectSource_Paypal_PaymentController extends Ebizmarts_SagePaySuite_PaymentController {

    /**
     * Create order action
     */
    public function onepageSaveOrderAction() {



        if ($this->_expireAjax()) {
            return;
        }

        //save order comment logic
        $orderComment = $this->getRequest()->getPost('order');
        Mage::getSingleton('core/session')->setOrderComment($orderComment['customer_comment']);

        $paymentData = $this->getRequest()->getPost('payment', array());
        if ($paymentData) {

            //Sanitize payment data
            array_walk($paymentData, array($this, "sanitize_string"));

            $this->getOnepage()->getQuote()->getPayment()->importData($paymentData);
        }

        $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
        /* if(!$paymentMethod){
          $post = $this->getRequest()->getPost();
          $paymentMethod = $post['payment']['method'];
          } */

        if (!$this->getOnepage()->getQuote()->isVirtual() && !$this->getOnepage()->getQuote()->getShippingAddress()->getShippingDescription()) {
            $result['success'] = false;
            $result['response_status'] = 'ERROR';
            $result['response_status_detail'] = $this->__('Please choose a shipping method');
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
        }

        if (TRUE || (FALSE === strstr(parse_url($this->_getRefererUrl(), PHP_URL_PATH), 'onestepcheckout')) && is_null($this->getRequest()->getPost('billing'))) { // Not OSC, OSC validates T&C with JS and has it own T&C
            # Validate checkout Terms and Conditions
            $result = array();
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['response_status'] = 'ERROR';
                    $result['response_status_detail'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
            }
            # Validate checkout Terms and Conditions

            //Fix issue #9595957091315
            if(!empty($paymentData) && !isset($paymentData['sagepay_token_cc_id'])) {
                $this->getSageSuiteSession()->setLastSavedTokenccid(null);
            }

        }
        else {

            /**
             * OSC
             */
            if (FALSE !== Mage::getConfig()->getNode('modules/Idev_OneStepCheckout')) {
            	Mage::log('OSC Save Billing', null, 'OSC_Comments.log', true);
                $this->_OSCSaveBilling();
            }
            /**
             * OSC
             */

            /**
             * IWD OPC
             */
            if (FALSE !== Mage::getConfig()->getNode('modules/IWD_OnepageCheckout')) {

                # Validate checkout Terms and Conditions
                $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();

                if ($requiredAgreements) {
                    $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                    if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                        $result['success'] = false;
                        $result['response_status'] = 'ERROR';
                        $result['response_status_detail'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                        $this->getResponse()->setBody(Zend_Json::encode($result));
                        return;
                    }
                }
                $this->_IWD_OPCSaveBilling();
            }
            /**
             * IWD OPC
             */
        }

        if ($dataM = $this->getRequest()->getPost('shipping_method', '')) {
            //$this->getOnepage()->saveShippingMethod($this->sanitize_string($dataM));
            $this->getOnepage()->saveShippingMethod($dataM);
        }

        //Magemaven_OrderComment
        $orderComment = $this->getRequest()->getPost('ordercomment');
        if (is_array($orderComment) && isset($orderComment['comment'])) {
            $comment = trim($orderComment['comment']);
            if (!empty($comment)) {
                $this->getSageSuiteSession()->setOrderComments($comment);
            }
        }
        //Magemaven_OrderComment

        if ($paymentMethod == 'sagepaypaypal') {
            $resultData = array(
                'success' => 'true',
                'response_status' => 'paypal_redirect',
                'redirect' => Mage::getModel('core/url')->addSessionParam()->getUrl('sgps/paypalexpress/go', array('_secure' => true))
            );

            return $this->getResponse()->setBody(Zend_Json :: encode($resultData));
        }

        if ($paymentMethod == 'sagepayserver') {
            $this->_forward('saveOrder', 'serverPayment', null, $this->getRequest()->getParams());
            return;
        } else if ($paymentMethod == 'sagepaydirectpro') {
            $this->_forward('saveOrder', 'directPayment', null, $this->getRequest()->getParams());
            return;
        } else if ($paymentMethod == 'sagepayform') {
            $this->_forward('saveOrder', 'formPayment', null, $this->getRequest()->getParams());
            return;
        } else {

            //As of release 1.1.18. Left for history purposes, if is not sagepay, post should not reach to this controller
            $this->_forward('saveOrder', 'onepage', 'checkout', $this->getRequest()->getParams());
            return;
        }
    }

}