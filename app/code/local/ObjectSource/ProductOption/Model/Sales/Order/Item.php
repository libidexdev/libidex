<?php
/**
 * Object Source
 *
 * @copyright   Copyright (c) 2015 Object Source.
 **/
class ObjectSource_ProductOption_Model_Sales_Order_Item extends Mage_Sales_Model_Order_Item
{
    /**
     * Get product options array.
     * Loads product option price.
     *
     * @return array
     */
    public function getProductOptions()
    {
        $options = parent::getProductOptions();
        if (count($options) && array_key_exists('options', $options)) {
            foreach ($options['options'] as &$_prodOption) {
                if (is_array($_prodOption) && array_key_exists('option_value', $_prodOption)) {
                    $optionPrice = Mage::getModel('os_productOption/catalog_product_option_type_price')->load(
                        $_prodOption['option_value'],
                        'option_type_id'
                    );

                    if ((float)$optionPrice->getPrice() != 0) {
                        $_prodOption['option_price'] = $optionPrice->getPrice();
                    }
                }
            }
        }

        return $options;
    }
}
