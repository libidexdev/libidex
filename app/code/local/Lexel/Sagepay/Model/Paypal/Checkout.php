<?php
class Lexel_Sagepay_Model_Paypal_Checkout extends Ebizmarts_SagePaySuite_Model_Paypal_Checkout
{
    /**
     * Update quote when returned from PayPal
     * @param Mage_Core_Controller_Request_Http $paypalData
     *
     * This override comments out the setDataUsingMethod() methods due to the characters in
     * the shipping address details having encoding issues in the admin panel when viewing orders
     * LMSD-2106
     */
    public function returnFromPaypal(Mage_Core_Controller_Request_Http $paypalData)
    {

        // import billing address (only if not already set in quote (incheckout paypal))
        $billingAddress = $this->_quote->getBillingAddress();

        $baddressValidation = $billingAddress->validate();
        if ($baddressValidation !== true) {
            foreach ($this->_billingAddressMap as $key=>$value) {
                $arvalue = $paypalData->getPost($key);
                if ($value == 'street') {
                    $arvalue = $paypalData->getPost('DeliveryAddress1') ."\n". $paypalData->getPost('DeliveryAddress2');
                }

                //$billingAddress->setDataUsingMethod($value, mb_convert_encoding($arvalue, 'UTF-8', 'ISO-8859-15'));
            }
        }

        // import shipping address
        if (!$this->_quote->getIsVirtual() && ($paypalData->getPost('AddressStatus') != 'NONE')) {
            $shippingAddress = $this->_quote->getShippingAddress();

            if ($shippingAddress) {
                foreach ($this->_billingAddressMap as $key=>$value) {
                    $arvalue = $paypalData->getPost($key);

                    if (empty($arvalue)) {
                        continue;
                    }

                    if ($value == 'street') {
                        $arvalue = $paypalData->getPost('DeliveryAddress1') ."\n". $paypalData->getPost('DeliveryAddress2');
                    }

                    //$shippingAddress->setDataUsingMethod($value, mb_convert_encoding($arvalue, 'UTF-8',
                    // 'ISO-8859-15'));
                }

                $this->_quote->getShippingAddress()->setCollectShippingRates(true);
            }
        }

        $this->_ignoreAddressValidation();

        // import payment info
        $payment = $this->_quote->getPayment();
        $payment->setMethod('sagepaydirectpro');

        $this->_quote->collectTotals()->save();
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * This override is just because the method has private access and is used
     * within the returnFromPaypal() method defined above
     */
    private function _ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }
}