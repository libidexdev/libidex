<?php

class ObjectSource_OrderFulfillment_Model_Sales_Order extends Mage_Sales_Model_Order
{
    public function getOrderItemsForOrderType()
    {
        $items = Mage::getModel("sales/order_item")->getCollection()
            ->addFieldToFilter("parent_item_id", array('null' => true))
            ->addFieldToFilter("order_id", $this->getEntityId());
        return $items;
    }

    public function getOrderStoreBasedOnRoleName()
    {
        $roleName = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleName();
        $storeId = 0;
        switch ($roleName)
        {
            case 'Malaysia': $storeId = 2; break;
            case 'London': $storeId = 1; break;
        }
        return Mage::app()->getStore($storeId);;
    }

    public function setOrderItemsPrintedForSupplier($supplier)
    {
        $_product = Mage::getModel('catalog/product');
        $attr = $_product->getResource()->getAttribute("supplier");

        $items = $this->getOrderItemsForOrderType();
        foreach ($items as $item)
        {
            $fulfillmentData = unserialize($item->getFulfillmentData());
            if ($attr->getSource()->getOptionText($fulfillmentData['supplier']) == $supplier)
            {
                $fulfillmentData['printed'] = 1;
            }
            $item->setFulfillmentData(serialize($fulfillmentData));
            $item->save();
        }
    }

    public function getFulfillmentDataValue($key)
    {
        $fulfillmentData = unserialize($this->getFulfillmentData());
        if (isset($fulfillmentData[$key]))
            return $fulfillmentData[$key];

        return null;
    }

    public function setFulfillmentDataValue($key, $value)
    {
        $fulfillmentData = unserialize($this->getFulfillmentData());
        $fulfillmentData[$key] = $value;
        $this->setFulfillmentData(serialize($fulfillmentData));
        $this->save();
    }
}