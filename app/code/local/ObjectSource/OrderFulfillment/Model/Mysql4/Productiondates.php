<?php
class ObjectSource_OrderFulfillment_Model_Mysql4_Productiondates extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('orderfulfillment/productiondates', 'productiondate_id');
    }

    public function getProductionDelay()
    {
        $totals = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_PROCESSING) //use state instead of status since 'processing' is a state of the order not a status.
            ->getColumnValues('base_subtotal');
        if (!empty($totals))
            $total = array_sum($totals);
        else
            $total = 0.00;

        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('processing_total_from<=:processing_total_from')
            ->where('processing_total_to>=:processing_total_to');

        $binds = array(
            'processing_total_from' => $total,
            'processing_total_to' => $total,
        );

        $row = $adapter->fetchRow($select, $binds);
        if ($row) {
            return $row['delayed'];
        }

        return 0;
    }
}