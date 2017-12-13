<?php
class ObjectSource_MalaysiaInvoice_Helper_CustomOption extends Mage_Core_Helper_Data
{
    /**
     * Tries to decode the Colour and Size options from sales item.
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Order_Item
     */
    public function decodeCustomOptions(Mage_Sales_Model_Order_Item $item)
    {
        $productOptions = $item->getProductOptions();

        $item->setColor('')->setSize('');

        if ($productOptions && is_array($productOptions) && count($productOptions)) {
            if(array_key_exists('options', $productOptions) && is_array($productOptions['options'])) {
                foreach ($productOptions['options'] as $option) {
                    if (isset($option['label'])) {
                        switch ($option['label']) {
                            case 'Colour Primary':
                            case 'Color Primary':
                                $item->setColor($option['value']);
                                break;

                            case 'Size Size':
                                $item->setSize($option['value']);
                                break;
                        }
                    }
                }
            } elseif(array_key_exists('attributes_info', $productOptions) && is_array($productOptions['attributes_info'])) {
                foreach ($productOptions['attributes_info'] as $option) {
                    if (isset($option['label'])) {
                        switch ($option['label']) {
                            case 'Colour Primary':
                            case 'Color Primary':
                                $item->setColor($option['value']);
                                break;

                            case 'Size Size':
                                $item->setSize($option['value']);
                                break;
                        }
                    }
                }
            } else {
                if ($item->getProduct()->getColor()) {
                    $item->setColor($item->getProduct()->getAttributeText('color'));
                }

                if ($item->getProduct()->getSize()) {
                    $item->setSize($item->getProduct()->getAttributeText('size'));
                }
            }
        }

        return $item;
    }
}
