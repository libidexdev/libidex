<?php

class Aitoc_Aitpagecache_Model_Observer_Cache extends Aitoc_Aitpagecache_Model_Observer
{
    /**
     * added in version 2.0.1
     * admin cache manipulation
     */
    public function massEnableCache($observer)
    {
        if(!$this->_helper()->hasAitpagecacheIndexFile())
        {
            foreach($_POST['types'] as $key=>$name)
            {
                if($name =='aitpagecache')
                {
                    unset($_POST['types'][$key]);
                }
            }
            $this->_helper()->throwUnrequiredIndexFileError();
            return $this;
        }
        $this->_changeConfigValue(1);
        $this->_updateCookieConfig();
        return $this;
    }

    public function massDisableCache($observer)
    {
        return $this->_changeConfigValue(0);
    }

    public function massRefreshCache($observer)
    {
        $cacheTypes = Mage::app()->getRequest()->getParam('types');
        if(in_array('aitpagecache', $cacheTypes))
        {
            // manually refresh cache data
            $this->_helper()->clearCache();
        }
        $this->_updateCookieConfig();
        return $this;
    }

    /**
     * clear cache when product or cms page edit or delete in adminhtml
     */
    public function clearAdminCurrentCache()
    {
        $this->_helper()->clearCache();
    }

    public static function flushSystemCache() {
        Mage::helper('aitpagecache')->clearCache(false);
    }
}