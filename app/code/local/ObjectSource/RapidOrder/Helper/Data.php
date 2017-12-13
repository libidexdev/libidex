<?php
class ObjectSource_RapidOrder_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getColumnParams()
    {
        return array(
            'header' => 'Rapid Order',
            'type' => 'options',
            'options' => $this->getRapidOrderOptions(),
            'renderer' => 'ObjectSource_RapidOrder_Block_Adminhtml_Renderer_Rapidselection',
            'filter_condition_callback' => array(
                $this, 'filterRapidOrder'
            ),
            'sortable' => false
        );
    }

    /**
     * Filter by the content of the select box, but rather than filtering using equality, use LIKE instead. This gives us
     * the ability to filter by "All rapid orders" by effectively searching for the string "RAPID".
     * 
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param $column
     */
    public function filterRapidOrder(Mage_Sales_Model_Resource_Order_Grid_Collection $collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addFieldToFilter('order.coupon_code', array('like' => "%$value%"));
    }

    public function getRapidOrderOptions()
    {
        return array(
            'RAPID' => 'All Rapid Orders' ,
            'RAPIDPLATINUM' => 'Only platinum' ,
            'RAPIDGOLD' => 'Only gold' ,
            'RAPIDSILVER' => 'Only silver'
        );
    }
    
    public function getQuoteCouponValue() {

        // check items in cart rather than coupon
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $items = $quote->getAllItems();

        foreach($items as $item) {

            if($item->getData('sku') == 'rapidSilver') {
                return 'SILVER';
            }
        }

        return 'STANDARD';
    }
}
