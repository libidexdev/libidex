<?php
class ObjectSource_RapidService_Model_Sales_Quote_Address_Total_RapidService
    extends Mage_Sales_Model_Quote_Address_Total_Abstract {
    /** Number of days to dispatch the order */
    const SILVER_SERVICE_DISPATCH = 14;

    /**
     * Returns the percentage surcharge value.
     *
     * @return int
     */
    protected function _getSurchargePercentage()
    {
        return (int)Mage::getStoreConfig('rapidservice/general/surcharge');
    }

    /**
     * Initialize the total.
     */
    public function __construct()
    {
        $this->setCode('os_rapidservice');
    }

    /**
     * Collect the Rapid Service total for the given quote address.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this|Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);

        // ignore surcharge if rapid service has not been selected.
        if (!Mage::helper('os_rapidservice')->isRapidService($address->getQuote())) {
            // reset the total state:
            foreach ($address->getAllItems() as $_item) {
                $_item->setBaseRapidserviceAmount(0);
                $_item->setRapidserviceAmount(0);
            }

            $this->_setAmount(0);
            $this->_setBaseAmount(0);
            $address->setRapidserviceAmount(0);

            return $this;
        }

        $_surchargeBase = 0;

        foreach ($address->getAllItems() as $_item) {
            $_product = $_item->getProduct()->load($_item->getProduct()->getId());

            if (!$_product->getOsRapidservice()) {
                // product not available for Rapid Service - do not apply surcharge
                //return $this;
                continue;
            }

            if($_product->getTypeId() != 'giftvoucher') {
                /** Get the full price of the product */
                $_fullProdPrice = $_item->getProduct()->getPrice() + $this->_calculateCustomOptionPrice($_item);
                $_surchargeItemBase = $_fullProdPrice * $_item->getQty() * ($this->_getSurchargePercentage() / 100);
                $_surchargeItem = Mage::app()->getStore($address->getQuote()->getStore())->convertPrice($_surchargeItemBase);

                $_item->setBaseRapidserviceAmount($_surchargeItemBase);
                $_item->setRapidserviceAmount($_surchargeItem);

                $_surchargeBase += $_surchargeItemBase;
            }
        }

        $_surcharge = Mage::app()->getStore($address->getQuote()->getStore())->convertPrice($_surchargeBase);

        $this->_addBaseAmount($_surchargeBase);
        $this->_addAmount($_surcharge);

        $address->setRapidserviceAmount($_surcharge);

        return $this;
    }

    /**
     * Collect custom option prices from the given item.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    protected function _calculateCustomOptionPrice($item) {
        $selectedOptions = $item->getOptions();

        $optPrice = 0;
        foreach ($selectedOptions as $_option) {
            if ($_option->getValue() && is_numeric($_option->getValue())) {
                $optionPrice = Mage::getModel('os_rapidservice/catalog_product_option_type_price')->load(
                    (int)$_option->getValue(),
                    'option_type_id'
                );


                if ('fixed' == $optionPrice->getPriceType()) {
                    $optPrice += $optionPrice->getPrice();
                } else {
                    $optPrice += $item->getPrice() * $optionPrice->getPrice() / 100;
                }
            }
        }

        return $optPrice;
    }

    /**
     * Fetch the total data.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this|array
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getOsRapidserviceAmount();
        if ($amount) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $this->getLabel(),
                'value' => $amount
            ));
        }
        return $this;
    }

    /**
     * Return the rapid service surcharge label.
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('os_rapidservice')->__('Silver Rapid');
    }
}
