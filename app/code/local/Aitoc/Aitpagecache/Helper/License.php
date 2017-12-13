<?php
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */
class Aitoc_Aitpagecache_Helper_License extends Aitoc_Aitsys_Helper_License
{
	protected $_sCacheConfig = '';
	
	public function __construct()
	{
        $this->_sCacheConfig = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'use_cache.ser';
    }

    public function uninstallBefore()
    {
    	$allTypes = Mage::app()->useCache();
        $allTypes['aitpagecache'] = 0;
        Mage::app()->saveUseCache($allTypes);
    	
        $this->writeUseCacheData(0);
    	
    	//Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Please do not forget to restore the original index.php from the backup.'));
    }
    
    public function installBefore()
    {
    	$allTypes = Mage::app()->useCache();
        $allTypes['aitpagecache'] = 0;
        Mage::app()->saveUseCache($allTypes);
        
        if(!function_exists('ait_cache_getFilePath'))
        {
            $error = Mage::helper('adminhtml')->__('Magento Booster: file "/index.php" is not replaced. Back up your index.php file and copy new index.php from the /magentobooster/ folder to the root directory of your Magento installation.' );
            Mage::getSingleton('adminhtml/session')->addError($error);
        }
        
        $notice = Mage::helper('adminhtml')->__('Please go to System -> Cache Management to enable Magento Booster');
        Mage::getSingleton('adminhtml/session')->addNotice($notice);
    }
    
	protected function writeUseCacheData($enabled = 1)
	{
        try
        {            
            if(file_exists($this->_sCacheConfig)) {
                $cacheFile = unserialize(file_get_contents($this->_sCacheConfig));
            }
            $cacheFile[Aitoc_Aitpagecache_Mainpage::DEFAULT_COOKIE_ID] = (int)$enabled;
            
            return file_put_contents($this->_sCacheConfig, serialize($cacheFile));
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}