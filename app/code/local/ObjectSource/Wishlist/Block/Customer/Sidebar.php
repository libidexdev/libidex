<?php

class ObjectSource_Wishlist_Block_Customer_Sidebar extends Mage_Wishlist_Block_Customer_Sidebar
{
    protected function _prepareCollection($collection)
    {
        $collection->setCurPage(1)
            ->setPageSize(15)
            ->setInStockFilter(true)
            ->setOrder('added_at');

        return $this;
    }
}