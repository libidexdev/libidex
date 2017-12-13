<?php
class Lexel_InventoryReport_Block_Inventory extends Mage_Core_Block_Template
{
    public function getTotalStockValue()
    {
        $totalValue = 0;
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('type_id')
            ->addAttributeToFilter('sku', array('like' => 'LEX-%'));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        foreach ($collection as $product) {
            $rowValue = $product->getQty() * $product->getPrice();
            $totalValue += $rowValue;
        }

        return $totalValue;
    }

    public function getTotalStockQty()
    {
        $totalQty = 0;
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('type_id')
            ->addAttributeToFilter('sku', array('like' => 'LEX-%'));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        foreach ($collection as $product) {
            $totalQty += $product->getQty();
        }

        return $totalQty;
    }
}