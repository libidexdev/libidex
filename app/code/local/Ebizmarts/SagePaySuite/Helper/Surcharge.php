<?php

class Ebizmarts_SagePaySuite_Helper_Surcharge extends Mage_Core_Block_Abstract {

    /**
     *
     * TODO: Antes de cargar amount al carro, validar que el selected payment method sea sagepaydirectpro
     *
     */
    public function getAmount($orderId) {
        $trn = Mage::getModel('sagepaysuite2/sagepaysuite_transaction')
                ->loadByParent($orderId);

        return $trn->getSurchargeAmount();
    }

    public function getChargeAmount($address) {

        //do nothing if disabled
        if(Mage::getStoreConfigFlag('payment/sagepaysurcharges/active',Mage::app()->getStore()) !== FALSE){

            //SERVER, DIRECT 3D, and FORM callback.
            $reg = Mage::registry('sageserverpost');
            if(!is_null($reg)) {
                return $reg->getData('Surcharge');
            }


            $quote = $address->getQuote();
            $store = $quote->getStore();

            $config = Mage::getSingleton('sagepaysuite/api_payment')
                ->getConfigData('surcharge_creditcards');

            $_config = unserialize($config);

            //Do nothing if no cards are configured for surcharge
            if ((count($_config) === 0) || (($quote->getPayment()->getMethod() != 'sagepaydirectpro') && ($quote->getPayment()->getMethod() != 'sagepaydirectpro_moto'))) {
                return FALSE;
            }

            $charge = NULL;
            $params = Mage::app()->getRequest()->getParam('payment', NULL);

            //Cc type from params if we are on checkout
            if(!is_null($params) && isset($params['cc_type']) && $params['cc_type'] != ""){
                $ccType = (string) $params['cc_type'];
            }else if(!is_null($params) && isset($params['cc_type_token']) && $params['cc_type_token'] != ""){
                $ccType = (string) $params['cc_type_token'];
            }else{
                $ccType = $quote->getPayment()->getCcType();
            }

            if(!is_array($_config)) {
                return false;
            }

            //Check if a rule exists for this card
            foreach ($_config as $rule) {

                if (($rule['creditcard'] == 'ALL') || ($ccType == $rule['creditcard'])) {
                    $charge = $rule;
                    break;
                }
            }

            if (!is_null($charge)) {

                $shipping = $address->getShippingAmount();

                if ((string) $quote->getPayment()->getConfigData('trncurrency') == 'store') {
                    $chargeAmountNoTax = $address->getSubtotal();
                    $chargeAmountTax   = $address->getGrandTotal();
                }
                else {
                    $chargeAmountNoTax = $address->getBaseSubtotal();
                    $chargeAmountTax   = $address->getBaseGrandTotal();
                }

                //Apply TAX if applies
                $taxClassId = (int) Mage::getStoreConfig('payment/sagepaysuite/surcharge_taxclass', $store);
                $percent = 0;

                if ($taxClassId !== 0) {
                    $trequest = Mage::getSingleton('tax/calculation')->getRateRequest($quote->getBillingAddress(), $quote->getShippingAddress(), FALSE, $store);
                    $percent  = Mage::getSingleton('tax/calculation')->getRate($trequest->setProductClassId($taxClassId));
                }

                if ($charge['chargetype'] == 'fixed') {
                    $amount = $charge['amount'];
                }
                else { //Percent
                    $amount = ( ( $charge['amount'] * ($chargeAmountNoTax + $shipping + $address->getTaxAmount()) ) / 100);
                }

                //Adding TAX
                $chargeTaxAmount = 0;
                if ($percent) {
                    $chargeTaxAmount = (($percent * $amount) / 100);
                    $amount = $amount + $chargeTaxAmount;
                }
                $this->_coreSession()->setData('surchargeamount', $amount);
                $this->_coreSession()->setData('surchargeamounttax', $chargeTaxAmount);
                Mage::getSingleton('sagepaysuite/session')->setSurcharge($amount + $chargeTaxAmount);

                return (float) $amount; //$store->roundPrice($shippingTax);
            }
            else {

                $this->_coreSession()->setData('surchargeamount', NULL);
                $this->_coreSession()->setData('surchargeamounttax', NULL);
                Mage::getSingleton('sagepaysuite/session')->setSurcharge(NULL);

                return 0.00;
            }
        }else{
            //surcharges disabled
            return null;
        }
    }

    /**
     * Format precision given amount/currency
     *
     */
    public function cur($value, $currencyCode) {
        $_currency = Mage::getModel('directory/currency')->load($currencyCode);
        return $_currency->formatPrecision($value, 2, array(), false);
    }

    protected function _coreSession() {
        return Mage::getSingleton('core/session');
    }

}