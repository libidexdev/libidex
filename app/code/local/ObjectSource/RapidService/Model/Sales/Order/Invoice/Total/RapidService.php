<?php
class ObjectSource_RapidService_Model_Sales_Order_Invoice_Total_RapidService
    extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collects the Rapid Service surcharge.
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this|Mage_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $invoice->setRapidserviceAmount(0);
        $invoice->setBaseRapidserviceAmount(0);

        $rapidServiceAmount = $invoice->getOrder()->getRapidserviceAmount();

        if ($rapidServiceAmount) {
            $invoice->setSurchargeAmount($rapidServiceAmount);
            $invoice->setBaseSurchargeAmount($rapidServiceAmount);

            $invoice->setGrandTotal( ($invoice->getGrandTotal()+$rapidServiceAmount) );
            $invoice->setBaseGrandTotal( ($invoice->getBaseGrandTotal()+$rapidServiceAmount) );
        }

        return $this;
    }
}
