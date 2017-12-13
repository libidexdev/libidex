<?php
class ObjectSource_RapidOrder_Block_Rapidselection extends Mage_Core_Block_Template
{
    public function getQuoteCouponValue() {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $code = $quote->getCouponCode();
        $websiteId = Mage::app()->getWebsite()->getId();
        if (!empty($code)) {
            preg_match("/^RAPID(.*)$websiteId/", $code, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            }
        }
        return 'STANDARD';
    }
}