<?php
class Aitoc_Aitpagecache_Block_Adminhtml_Warmupcache extends Mage_Adminhtml_Block_Template
{
    /**
     * Get clean cache url
     *
     * @return string
     */
    public function getWarmupStartUrl()
    {
        return $this->getUrl('adminhtml/aitpagecache/warmupstart');
    }

    public function getStatus()
    {
        $helper = Mage::helper('aitpagecache/warmup');
        $data = $helper->getWarmupSetting();
        if($data['isEnable'] != 1)
        {
            return Mage::helper('aitpagecache')->__('No active process');
        }

        return $data['position'].' from '.$data['all'].' cached';
    }
}