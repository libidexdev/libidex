<?php 
class ObjectSource_RapidOrder_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Add the coupon code to the order collection (without rewrites!).
     *
     * @param $observer
     */
    public function salesOrderGridCollectionLoadBefore($observer)
    {
        // Add the coupon code into the collection call.
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = $observer->getOrderGridCollection();
        /** @var Zend_Db_Select $select */
        $select = $collection->getSelect();

        // Get the coupon code via a join on sales_flat_order. The sub-select is necessary since otherwise we run into
        // Ambiguous columns.
        $resource = Mage::getSingleton('core/resource');
        $salesOrderTable =  $resource->getTableName('sales/order');
        $readAdapter = $resource->getConnection('core_read');
        $couponSelect = $readAdapter->select()
            ->from($salesOrderTable, array('entity_id', 'coupon_code'));

        $select->joinLeft(array('order' => $couponSelect), 'main_table.entity_id = order.entity_id');
    }
}
