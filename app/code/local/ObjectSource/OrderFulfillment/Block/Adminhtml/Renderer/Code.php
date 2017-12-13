<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Renderer_Code extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private static $_supplierOptions = null;

    public function render(Varien_Object $row)
    {
        if (empty(self::$_supplierOptions))
        {
            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter('supplier')->getFirstItem();
            $attributeId = $attributeInfo->getAttributeId();
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
            $attributeOptions = $attribute ->getSource()->getAllOptions(false);
            self::$_supplierOptions = $attributeOptions;
        }

        $items = $row->getOrderItemsForOrderType();

        $suppliers = self::$_supplierOptions;
        $codes = array();
        foreach ($suppliers as $supplier)
        {
            foreach ($items as $item)
            {
                $fulfillmentData = unserialize($item->getFulfillmentData());
                if ($fulfillmentData['supplier'] == $supplier['value'])
                {
                    $codes[$supplier['label']] = 1;
                }
            }
        }

        if ((!empty($codes['Malaysia'])) && (!empty($codes['London']))) {
            $html = 'H';
        }
        else if (!empty($codes['Malaysia'])) {
            $html = 'M';
        }
        else if (!empty($codes['London'])) {
            $html = 'L';
        }
        else {
            $html = 'E';
        }

        return $html;
    }
}