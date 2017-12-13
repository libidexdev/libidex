<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Renderer_Lineitems extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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

            //Mage::log('collecting suppliers', null, 'OS_OrderFulfillment.log');
        }

        // Dont display if not approved
        $order = Mage::getModel('sales/order')->load($row->getEntityId());
        $approved = $order->getFulfillmentDataValue('approved');
        if ((Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleName() == 'Malaysia') && empty($approved)) {
            return '';
        }

        $items = $order->getOrderItemsForOrderType();

        $suppliers = self::$_supplierOptions;
        foreach ($suppliers as &$supplier)
        {
            if (Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleName() == $supplier['label'])
            {
                $supplier['permitted'] = 1;
            }

            $supplier['printed'] = 0;
            $supplier['total'] = 0;
            foreach ($items as $item)
            {
                $fulfillmentData = unserialize($item->getFulfillmentData());
                if ($fulfillmentData['supplier'] == $supplier['value'])
                {
                    if (!empty($fulfillmentData['printed']))
                    {
                        $supplier['printed']++;
                    }
                    $supplier['total']++;
                }
            }
            $supplier['unprinted'] = $supplier['total'] - $supplier['printed'];
        }

        // Render array
        $html = '';
        foreach ($suppliers as &$supplier)
        {
            if (!empty($supplier['permitted']))
            {
                if (empty($supplier['unprinted']))
                {
                    $color = '#5C8F1B'; //green
                }
                else
                {
                    $color = '#ee372a'; //red
                }
            }
            else
            {
                $color = '#575757'; //grey
            }

            $html .= '<span style="color:'.$color.'">';
            $html .= $supplier['label'] . ' Print Pending (' . $supplier['unprinted'] . ')<br>';
            $html .= '</span>';
        }

        return $html;
    }
}