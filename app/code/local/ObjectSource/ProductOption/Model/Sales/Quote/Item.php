<?php
class ObjectSource_ProductOption_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function getProductOptions()
    {
        try {
            $options = array();
            foreach ($this->getOptions() as $option) {
                if ($option->getCode() == 'info_buyRequest') {
                    $options = unserialize($option->getValue());
                    break;
                }
            }

            if (count($options) && array_key_exists('options', $options)) {
                foreach ($options['options'] as $key => $val) {
                    $optionPrice = Mage::getModel('os_productOption/catalog_product_option_type_price')->load(
                        $val,
                        'option_type_id'
                    );


                    if ((float)$optionPrice->getPrice() != 0) {
                        $options['options'][$key] = $optionPrice->getPrice();
                    } else {
                        $options['options'][$key] = 0;
                    }
                }
            }

            return isset($options['options']) ? $options['options'] : null;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
