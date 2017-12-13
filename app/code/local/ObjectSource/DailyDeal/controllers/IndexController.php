<?php

class ObjectSource_DailyDeal_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        set_time_limit(0);
        Mage::helper('dailydeal')->run();
    }
    public function rssAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml', true);
        $block = $this->getLayout()->createBlock('dailydeal/rss');
        echo $block->toHtml();
    }

    public function showAction()
    {
        echo $this->getLayout()->createBlock('dailydeal/promotion')->getStaticBlock();
        exit;
    }
}