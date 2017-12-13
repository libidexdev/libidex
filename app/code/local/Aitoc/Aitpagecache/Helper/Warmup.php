<?php
class Aitoc_Aitpagecache_Helper_Warmup extends Mage_Core_Helper_Abstract
{
    protected $_fileName = '';

    public function getFileName()
    {
        if(empty($this->_fileName))
        {
            $this->_fileName = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'warm_up.ser';
        }
        return $this->_fileName;
    }

    public function saveWarmupSetting($data = array())
    {
        if(empty($data))
        {
            return false;
        }
        if( $data['position'] <= $data['all'])
        {
            file_put_contents($this->getFileName(), serialize($data));
        }
        else
        {
            unlink($this->getFileName());
        }
    }

    public function getWarmupSetting()
    {
        if(!file_exists($this->getFileName()))
        {
            return array('isEnable'=>0);
        }

        $data = file_get_contents($this->getFileName());
        return unserialize($data);
    }

    public function getWarmupCount()
    {
        $count = Mage::getStoreConfig('aitpagecache/config/warmup_count');
        if(empty($count))
        {
            $count = 10;
        }
        return $count;
    }
}