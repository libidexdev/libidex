<?php
class Aitoc_Aitpagecache_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_helper = null;

    protected $_sCacheConfig = '';
    protected $_cookieConfigPath = '';
    protected $_overrideConfig = null; //used after config values is updated in backend
    protected $_noRequiredConfigValues = array(
        'disallow_clear_cache_level',
        'disallow_bots_level',
        'block_exclude',
        'enable_debug',
    );
    protected $_allowedFieldsFromConfig = array(
        'cookie_restriction'
    );

    public function __construct() {
        $this->_sCacheConfig = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'use_cache.ser';
        $this->_cookieConfigPath = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'cookie_config.ser';
        parent::__construct();
    }

    public function replacePageHeaderTemplate($observer)
    {
        $block = $observer->getBlock();
        $type = $block->getType();
        if ($type == 'page/html_header') 
        {
            if(version_compare(Mage::getVersion(), '1.7.0.2', '<='))
            {
                $block->setWelcome($block->getChildHtml('welcome'));
            }
        }
    }

    protected function _helper() {
        if(is_null($this->_helper)) {
            $this->_helper = Mage::helper('aitpagecache');
        }
        return $this->_helper;
    }

    protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    protected function _changeConfigValue($toValue)
    {
        $cacheTypes = Mage::app()->getRequest()->getParam('types');

        if($cacheTypes)
        {
            if(in_array('aitpagecache', $cacheTypes))
            {
                // write use_cache.ser file like in Magento 1.3.* versions
                //if(!$this->_writeFileData($this->_sCacheConfig, serialize(array('aitpagecache' => (int)$toValue))))
                if(!$this->_saveConfigValue( array('aitpagecache' => (int)$toValue) ) )
                {
                    Mage::getSingleton('adminhtml/session')->addError($this->_helper()->__('Error while disabling Magento Booster cache. File %s is not writable.', $this->_sCacheConfig));

                    foreach($cacheTypes as $key => $value)
                    {
                        if($value == 'aitpagecache') {
                            unset($cacheTypes[$key]);
                        }
                    }
                    Mage::app()->getRequest()->setParam('types', $cacheTypes);
                }
            }
        }
        return $this;
    }

    protected function _saveConfigValue( $data = array() )
    {
        if(is_null($this->_overrideConfig)) {
            $this->_overrideConfig = $this->_helper()->getBoosterConfig();
        }
        if(!isset($data['aitpagecache'])) {
            $cache = $this->_helper()->getBooster()->getCacheConfigFile();
            if($cache !== null && isset($cache['aitpagecache'])) {
                $data['aitpagecache'] = $cache['aitpagecache'];
            }
        }
        $data = $this->_checkAndUpdateConfigValue($data, Aitoc_Aitpagecache_Mainpage::QUOTE_VALUE);
        $data = $this->_checkAndUpdateConfigValue($data, Aitoc_Aitpagecache_Mainpage::LOGIN_VALUE);
        $data = $this->_checkAndUpdateConfigValue($data, Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES);
        $data = $this->_checkAndUpdateConfigValue($data, Aitoc_Aitpagecache_Mainpage::EXCLUDED_PAGES);
        $data = $this->_checkAndUpdateConfigValue($data, Aitoc_Aitpagecache_Mainpage::ENABLE_DEBUG);

        //Checking if cookie restrictions are set and used in magento. If they are set - another cache flag will be used on frontend
        $data = $this->_checkAndUpdateConfigValue($data, Aitoc_Aitpagecache_Mainpage::RESTRICTION_COOKIE);

        $excluded_pages = Mage::getStoreConfig('aitpagecache/config/excluded_pages');
        $data = $this->_updateUndefinedConfigValue($data, Aitoc_Aitpagecache_Mainpage::EXCLUDED_PAGES, $excluded_pages, true);

        $excluded_pages = $data[Aitoc_Aitpagecache_Mainpage::EXCLUDED_PAGES];
        $data = $this->_updateDefinedConfigValue($data, Aitoc_Aitpagecache_Mainpage::EXCLUDED_PAGES, $excluded_pages, true);

        $enable_debug = (int)Mage::getStoreConfig('aitpagecache/config/enable_debug');
        $data = $this->_updateUndefinedConfigValue($data, Aitoc_Aitpagecache_Mainpage::ENABLE_DEBUG, $enable_debug, false);

        $cookie_restriction = (int)Mage::getStoreConfig('web/cookie/cookie_restriction');
        $data = $this->_updateUndefinedConfigValue($data, Aitoc_Aitpagecache_Mainpage::RESTRICTION_COOKIE, $cookie_restriction, false);

        $admin_area_names = $data[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES];
        $data = $this->_updateDefinedConfigValue($data, Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES, $admin_area_names, true);

        //Checking some not required variables
        foreach($this->_noRequiredConfigValues as $key) {
            $data = $this->_checkAndUpdateConfigValue($data, $key);
        }

        return $this->_writeFileData($this->_sCacheConfig, serialize($data));
    }

    protected function _checkAndUpdateConfigValue($data, $value) {
        if(isset($data[$value])) {
            //value is taken from function params and not changed
            $this->_overrideConfig[$value] = $data[$value];
        } elseif ( isset($this->_overrideConfig[$value]) ) {
            //taking value from config or previously updated config
            $data[$value] = $this->_overrideConfig[$value];
        }
        return $data;
    }

    protected function _updateUndefinedConfigValue($data, $name, $value, $toArray) {
        if(!isset($data[$name])) {
            $data[$name] = $toArray ? array_map('trim', preg_split("/\n|,/", $value)) : $value;
        }
        return $data;
    }

    protected function _updateDefinedConfigValue($data, $name, $value, $toArray) {
        if(!empty($data[$name]) && !is_array($data[$name]))
        {
            $data[$name] = $toArray ? array_map('trim', preg_split("/\n|,/", $value)) : $value;
        }
        return $data;
    }

    protected function _writeFileData($file, $data)
    {
        try
        {
            return file_put_contents($file, $data);
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * retrieve cookie domain data from magento config and put into .ser file like baseurl => cookie domain
     */
    protected function _updateCookieConfig()
    {
        //prepare data
        $data = array();
        $stores = Mage::app()->getStores();

        foreach ($stores as $store)
        {
            $unsecureBaseUrl = $store->getConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
            if(!key_exists($unsecureBaseUrl, $data))
            {
                if(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN != '')
                {
                    $data[$unsecureBaseUrl] = $store->getConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN);
                }
                elseif($unsecureBaseUrl)
                {
                    $data[$unsecureBaseUrl] = $unsecureBaseUrl;
                }
            }
            $secureBaseUrl = $store->getConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL);
            if(!key_exists($secureBaseUrl, $data))
            {
                if(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN != '')
                {
                    $data[$secureBaseUrl] = $store->getConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN);
                }
                elseif($secureBaseUrl)
                {
                    $data[$secureBaseUrl] = $secureBaseUrl;
                }
            }
        }

        if(!$this->_writeFileData($this->_cookieConfigPath, serialize($data)))
        {
            Mage::getSingleton('adminhtml/session')->addNotice($this->_helper()->__('Error while refreshing Magento Booster cookies cache. File %s is not writable.', $this->_cookieConfigPath));
        }

        return $this;
    }

    public function recalculateQuoteItems($observer) {
        if($observer && ( ($observer->getQuoteItem() && $observer->getQuoteItem()->getQuote()) || $observer->getQuote()  )  ) {
            if(false == $this->_helper()->isEnabledForQuote()) {
                return $this;
            }
            $quote = $observer->getQuoteItem() ? $observer->getQuoteItem()->getQuote() : $observer->getQuote();
            $amount = $this->_helper()->countQuoteItems($quote);
            if(isset($_COOKIE[Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID])) {
                $this->_helper()->delCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID);
            }
            $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CART_ID, $amount);
        }
        return $this;
    }
    
    public function updateCart($observer)
    {
        $qty = $observer->getCart()->getQuote()->getItemsQty();
        if($qty == 0)
        {
            $this->_helper()->delCacheCookie(Aitoc_Aitpagecache_Mainpage::NOT_EMPTY_CART);
        }
        else 
        {
            $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::NOT_EMPTY_CART, Aitoc_Aitpagecache_Mainpage::NOT_EMPTY_CART);
        }
    }

    public function onCheckoutCartProductAddAfter($observer) {
        if($this->_helper()->isEnabledForQuote()) {
            $this->recalculateQuoteItems($observer);
        } else {
            $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::NOT_EMPTY_CART, Aitoc_Aitpagecache_Mainpage::NOT_EMPTY_CART);
        }
        return $this;
    }

    public static function clearCache() {
        Mage::helper('aitpagecache')->clearCache(true);
    }   
  
    public function onBlockRendered($observer)
    {
        $helper = $this->_helper();
        
        if (!$helper->isJSLoaderAllowed())
        {
            return;
        }
        
        $transport = $observer->getData('transport');
        $block = $observer->getData('block');
        
        $className = get_class($block);
        
        $disabledBlocks = (array) $helper->getDisabledCacheBlocks(false);

        if (in_array($className, $disabledBlocks))
        {        
            $transport->setData('html', '<span class="aitoc-aitpagecache-loadable-block" id="aitoc-aitpagecache-loadable-block-'. $className . '">' . $transport->getData('html') .  '</span>');
        }
    }
    
    public function webConfigSectionChanged()
    {
        Mage::helper('aitpagecache/target')->clearMainPageCache();
        return $this->_updateCookieConfig();
    }

    public function webConfigSectionDesignChanged()
    {
        $_types = Mage::app()->getCacheInstance()->getInvalidatedTypes();
        $_types[] = 'aitpagecache';
        Mage::app()->getCacheInstance()->invalidateType($_types);
    }

    public function setRequestFormKey()
    {
        if (version_compare(Mage::getVersion(), '1.8.0.0', '>='))
        {
            if (file_exists($this->_helper()->getBooster()->getCacheFilePathByUrl(Mage::app()->getRequest()->getServer('HTTP_REFERER'), true)))
            {
                Mage::app()->getRequest()->setParam('form_key', Mage::getSingleton('core/session')->getFormKey());
            }
        }
    }
}