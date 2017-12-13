<?php

class ObjectSource_DailyDeal_Model_Observer
{
    public function run()
    {
        set_time_limit(0);
        Mage::log('Observer::run', null, 'OS_DailyDeal.log');
        Mage::helper('dailydeal')->run();
    }
}