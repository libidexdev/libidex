<?php
class ObjectSource_OrderOptions_Block_Adminhtml_Sales_Items_Column_OrderOptions extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{

    public function getOrderOptions()
    {
        $result = array();
        $item = $this->getData('item');
        if ($options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct())) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

}
