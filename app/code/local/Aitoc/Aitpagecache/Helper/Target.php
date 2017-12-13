<?php
class Aitoc_Aitpagecache_Helper_Target extends Aitoc_Aitpagecache_Helper_Data
{
    public function getCacheFilePath($pageUrl)
    {
        $booster = Mage::helper('aitpagecache')->getBooster();
        $path = $booster->getCacheFilePathByUrl($pageUrl);
		return $path;
    }
    
    public function getPageLocalPath($pageUrl)
    {
    	$path = $this->getCacheFilePath($pageUrl);
    	if ($path)
    	{
	    	$isMobile = basename(dirname(dirname($path))) == 'mobile' || basename(dirname(dirname($path))) == 'tablet' ? true : false;
            $localPath = basename(dirname($path)).DS.basename($path);
            $path = dirname(dirname($path));
            if ($isMobile)
	    	{
				$localPath = basename($path).DS.$localPath;
                $path = dirname($path);
	    	}
            if (!empty($_COOKIE["currency"]))
            {
                $localPath = basename($path).DS.$localPath;
            }
            return $localPath;
    	}
    	else 
    	{
    		return '';
    	}
    }
    
    public function clearCacheTarget($pages)
    {
    	foreach ($pages as $pageUrl)
    	{
    		$path = $this->getCacheFilePath($pageUrl);

    		Mage::helper('aitpagecache')->_emptyFullPath(basename($path), dirname($path));
    		
    		if (strpos($path, '/media/pages/'))
    		{
	    		$pathMobile = str_replace('/media/pages/', '/media/pages/mobile/', $path);
	    		Mage::helper('aitpagecache')->_emptyFullPath(basename($pathMobile), dirname($pathMobile));
	    		
	    		$pathTablet = str_replace('/media/pages/', '/media/pages/tablet/', $path);
	    		Mage::helper('aitpagecache')->_emptyFullPath(basename($pathTablet), dirname($pathTablet));
    		}
    	}    		
    }
    
    public function clearCacheTargetByProductId($productId)
    {
    	$pages = Mage::getModel('aitpagecache/target')->getPagesByProductId($productId);
    	
    	$booster = Mage::helper('aitpagecache')->getBooster();
    	$booster->_cacheFileParams['requestUri'] = '';
    	$cachePath = dirname(dirname($booster->getCacheFilePath()));

        if(!empty($_COOKIE["currency"]))
        {
            $cachePath = dirname($cachePath);
        }

        $isMobile = basename($cachePath) == 'mobile' || basename($cachePath) == 'tablet' ? true : false;
    	if ($isMobile)
    	{
            $cachePath = dirname($cachePath);
    	}
    	
    	foreach ($pages as $page)
    	{
	    	$filePath = $cachePath.DS.$page['page_path'];
	    	Mage::helper('aitpagecache')->_emptyFullPath(basename($filePath), dirname($filePath));
    	}
    	Mage::getModel('aitpagecache/target')->removePagesByProductId($productId);
    }

    public function clearMainPageCache()
    {
        $url = Mage::getBaseUrl('web');
        $pages = array($url);
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $pages[] = $url.$store->getCode();
                }
            }
        }
        $this->clearCacheTarget($pages);
    }
}