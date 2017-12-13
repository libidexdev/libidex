<?php
class ObjectSource_RapidService_Block_Sales_Order_Totals_RapidService extends Mage_Core_Block_Abstract
{
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Add this total to parent
     */
    public function initTotals()
    {
        if ((float)$this->getSource()->getRapidserviceAmount() == 0) {
            return $this;
        }
        $total = new Varien_Object(array(
            'code'  => 'os_rapidservice',
            'field' => 'rapidservice_amount',
            'value' => $this->getSource()->getRapidserviceAmount(),
            'label' => $this->__('Rapid Order'),
            'area'  => 'footer'
        ));
        $this->getParentBlock()->addTotalBefore($total, 'shipping');
        return $this;
    }
}
