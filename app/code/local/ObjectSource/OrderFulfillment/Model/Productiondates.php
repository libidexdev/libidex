<?php
class ObjectSource_OrderFulfillment_Model_Productiondates extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('orderfulfillment/productiondates');
    }

    public function getProductionDelay()
    {
        return $this->getResource()->getProductionDelay();
    }
}
