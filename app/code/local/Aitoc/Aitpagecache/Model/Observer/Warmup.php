<?php

class Aitoc_Aitpagecache_Model_Observer_Warmup extends Aitoc_Aitpagecache_Model_Observer
{
    protected $_limit = 10;

    public function run()
    {
        $helper = Mage::helper('aitpagecache/warmup');
        $data = $helper->getWarmupSetting();
        if($data['isEnable'] != 1)
        {
            return false;
        }
        $this->_limit = $helper->getWarmupCount();
        $collection = Mage::getResourceModel('core/url_rewrite_collection');
        $this->_collectionSetPage($collection, $data['position']);

        foreach($collection->load() as $urlRewrite)
        {
            $store = Mage::app()->getStore($urlRewrite->getStoreId());
            $baseUrl = $store->getBaseUrl();
            $urlIndex = $baseUrl.$urlRewrite->getRequestPath();
            $urlSimple = str_replace('/index.php', '', $baseUrl).$urlRewrite->getRequestPath();
            $this->_createCurlObject($urlIndex, $store->getCode());
            $this->_createCurlObject($urlSimple, $store->getCode());
        }

        $data['position'] += $this->_limit;
        $helper->saveWarmupSetting($data);
    }

    protected function _collectionSetPage($collection, $position)
    {
        $page = round($position/$this->_limit) +1;
        $collection->setPageSize($this->_limit)->setCurPage($page);
    }

    protected function _createCurlObject($url, $store=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($store))
        {
            curl_setopt($ch, CURLOPT_COOKIE, 'store='.$store.';');
        }
        if($store == 'default')
        {
            $this->_createCurlObject($url);
        }
        curl_exec($ch);
        curl_close($ch);
        return $ch;
    }
}