<?php

class ObjectSource_Measurement_Model_Observer
{

    public function modifyWebformresult($observer)
    {
        Mage::log($observer->getEvent(), null, 'os-webform.log');
    }

} 
