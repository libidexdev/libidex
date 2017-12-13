<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 30/09/16
 * Time: 10:16
 */
class Lexel_EcomdevCheckout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping {

    public function getCountryHtmlSelect($type)
    {
        $address = $this->getQuote()->getShippingAddress();
        $countryId = $address->getCountryId();
//        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        if ($type === 'shipping') {
            $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();
    }
}