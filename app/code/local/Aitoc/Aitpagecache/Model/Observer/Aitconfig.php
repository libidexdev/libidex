<?php

class Aitoc_Aitpagecache_Model_Observer_Aitconfig extends Aitoc_Aitpagecache_Model_Observer
{
    /**
     * Used to update booster cache.ser file on some config changes in backend.
     * @param type $observer
     */
    public function magentoConfigChanged($observer)
    {
        $data = $observer->getConfigData();
        if(!$data) {
            $data = $observer->getDataObject();
        }
        if(!is_object($data) || $data->getField() == '' || $data->getScope()!='default') {
            //can't find any config object
            return;
        }
        if(!in_array($data->getField(), $this->_allowedFieldsFromConfig)) {
            //we need to update config only on some fields, don't need it on every config value in magento
            return;
        }
        if(is_null($this->_overrideConfig)) {
            //saving default booster values if they are not set
            $this->_overrideConfig = $this->_helper()->getBoosterConfig();
        }
        //saving new data to overrideArray, because it's may not be saved in database yet and Mage::getConfig() will return old value
        $this->_overrideConfig[$data->getField()] = $data->getValue();
        //updating cache file
        $this->_saveConfigvalue();
    }

    public function aitpagecacheConfigChanged($observer)
    {
        if($observer->getField()) {
            if(!$this->_saveConfigvalue( array($observer->getField() => /*(int)*/$observer->getValue() ))) {
                Mage::getSingleton('adminhtml/session')->addError($this->_helper()->__('Error while saving Magento Booster config. File %s is not writable.', $this->_sCacheConfig));
            }
        }
    }

    public function saveAdminRoutersToConfig()
    {
        if(Mage::app()->useCache('config'))
        {
            $routers = Mage::getConfig()->getNode('admin/routers');
            foreach((array)$routers as $route)
            {
                $admin_area_names[] = (string)$route->args->frontName;
            }

            $config = unserialize(file_get_contents($this->_sCacheConfig));
            $config[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES] = array();
            $config[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES] = array_merge($config[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES], $admin_area_names);
            $config[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES] = array_unique($config[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES]);

            return $this->_writeFileData($this->_sCacheConfig, serialize($config));
        }
    }
}