<?php
class ObjectSource_RapidService_Block_Adminhtml_Sales_Order_Creditmemo_Totals
    extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals
{
    /**
     * Add Rapid Service to credit memo totals.
     *
     * @return $this|Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $amount = (float)$this->getSource()->getOrder()->getRapidserviceAmount();
        if ($amount) {
            $this->addTotalBefore(new Varien_Object(array(
                'code'      => 'os_rapidservice',
                'value'     => $amount,
                'base_value'=> $amount,
                'label'     => $this->helper('os_rapidservice')->__('Silver Rapid'),
            ), array('shipping', 'tax')));
        }

        return $this;
    }
}
