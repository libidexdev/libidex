<?php
class ObjectSource_RapidService_Model_Sales_Order_CreditMemo_Total_RapidService
    extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collects the Rapid Service surcharge.
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return $this|Mage_Sales_Model_Order_Creditmemo_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setRapidserviceAmount(0);
        $creditmemo->setBaseRapidserviceAmount(0);

        $rapidServiceAmount = $creditmemo->getOrder()->getRapidserviceAmount();

        if ($rapidServiceAmount) {
            $creditmemo->setSurchargeAmount($rapidServiceAmount);
            $creditmemo->setBaseSurchargeAmount($rapidServiceAmount);

            $creditmemo->setGrandTotal( ($creditmemo->getGrandTotal()+$rapidServiceAmount) );
            $creditmemo->setBaseGrandTotal( ($creditmemo->getBaseGrandTotal()+$rapidServiceAmount) );
        }

        return $this;
    }
}