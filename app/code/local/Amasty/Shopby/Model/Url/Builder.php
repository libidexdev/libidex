<?php

class Amasty_Shopby_Model_Url_Builder
{
    /** @var  string */
    public $moduleName;

    /** @var  int */
    public $mode;

    /** @var  array */
    protected $query;

    /** @var  array */
    protected $effectiveQuery;

    /** @var  string */
    protected $brandAttributeCode;

    /** @var Mage_Catalog_Model_Category|int */
    public $category;

    public function reset()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');

        $this->moduleName = Mage::app()->getRequest()->getModuleName();
        $this->category = $helper->getCurrentCategory();
        $this->query = Mage::app()->getRequest()->getQuery();
        $this->brandAttributeCode = Mage::getStoreConfig('amshopby/brands/attr');
        $this->mode = Mage::getStoreConfig('amshopby/seo/urls');
    }

    public function clearQuery()
    {
        $query = array();
        if ($this->isSomeSearch() && isset($this->query['q'])) {
            $query['q'] = $this->query['q'];
        }
        $this->query = $query;
    }

    public function clearPagination()
    {
        $pager = Mage::getBlockSingleton('page/html_pager');
        if (is_object($pager)) {
            $var = $pager->getPageVarName();
            if (isset($this->query[$var])) {
                unset($this->query[$var]);
            }
        }
    }

    public function clearCategory()
    {
        $this->category = Mage::app()->getStore()->getRootCategoryId();
    }

    public function changeQuery(array $delta)
    {
        $this->query = array_merge($this->query, $delta);
    }

    public function getUrl()
    {
        $this->updateEffectiveQuery();

        $paramPart = $this->getParamPart();
        $basePart = $this->getBasePart($paramPart);

        $url = $basePart . $paramPart;
        $url = preg_replace('|(^:)/{2,}|', '$1/', $url);

        return $url;
    }

    protected function updateEffectiveQuery()
    {
        $this->effectiveQuery = $this->query;

        $getParamCategory = $this->isNewOrSale() || $this->getCurrentLandingKey() || $this->isSomeSearch();
        if ($getParamCategory) {
            if ($this->getCategoryId() != (int) Mage::app()->getStore()->getRootCategoryId()) {
                $this->effectiveQuery['cat'] = $this->getCategoryId();
            } else {
                $this->effectiveQuery['cat'] = null;
            }
        }

        $this->excludeParams();
        $this->cleanNulls();
        $this->sortQuery();
        $this->detectMultiselectParam();

    }

    protected function excludeParams()
    {
        $excludeParamsStr = trim(Mage::getStoreConfig('amshopby/seo/query_param_exclude'));
        if ($excludeParamsStr != '') {
            $excludeParams = array_intersect(explode(',', $excludeParamsStr), array_keys($this->effectiveQuery));
            foreach ($excludeParams as $param) {
                unset($this->effectiveQuery[$param]);
            }
        }
    }

    protected function cleanNulls()
    {
        foreach ($this->effectiveQuery as $k => &$v){
            if (is_null($v)) {
                unset($this->effectiveQuery[$k]);
                continue;
            }
        }
    }

    protected function sortQuery()
    {
        foreach ($this->effectiveQuery as &$v){
            //sort values to avoid duplicate content
            if (is_array($v)) {
                sort($v);
            }
        }

        if ($this->brandAttributeCode && isset($this->effectiveQuery[$this->brandAttributeCode])) {
            uksort($this->effectiveQuery, array($this, 'compareParamsPriority'));
        } else {
            ksort($this->effectiveQuery);
        }
    }

    protected function compareParamsPriority($a, $b)
    {
        if ($a == $this->brandAttributeCode) {
            return -1;
        } else if ($b == $this->brandAttributeCode) {
            return 1;
        } else {
            return strcmp($a, $b);
        }
    }

    protected function detectMultiselectParam()
    {
        $paramName = Mage::getStoreConfig('amshopby/seo/query_param');
        if ($paramName) {
            $foundMultipleValues = false;
            foreach ($this->query as $v) {
                if (is_array($v)) {
                    $foundMultipleValues = true;
                    break;
                }
            }
            if ($foundMultipleValues){
                $this->query[$paramName] = 'true';
            }
            else {
                $this->query[$paramName] = null;
                unset($this->query[$paramName]);
            }
        }
    }

    protected function getParamPart()
    {
        $seoParts = array();
        $query = array();
        // add attributes as keys, not as ids
        if ($this->mode && !$this->isSomeSearch()) {
            $options = $this->getUrlHelper()->getAllFilterableOptionsAsHash();
            foreach ($this->effectiveQuery as $attrCode => $ids)
            {
                $attrCode = str_replace(array('_', '-'), Mage::getStoreConfig('amshopby/seo/special_char'), $attrCode);

                if (isset($options[$attrCode])){ // it is filterable attribute
                    if ($this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT) {
                        $part = $this->getUrlHelper()->_formatAttributePartShort($attrCode, $ids);
                    } else {
                        $part = $this->getUrlHelper()->_formatAttributePartMultilevel($attrCode, $ids);
                    }

                    if (strlen($part)) {
                        $seoParts[] = $part;
                    }
                }
                else {
                    $query[$attrCode] = $ids; // it is pager or smth else
                }
            }
        } else {
            $query = $this->effectiveQuery;
        }

        $glue = ($this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT) ? Mage::getStoreConfig('amshopby/seo/option_char') : '/';
        $result = implode($glue, $seoParts);
        if (strlen($result)) {
            $result = $this->getUrlHelper()->checkAddSuffix($result);
        }

        // add other params as query string if any
        $query = http_build_query($query);
        if (strlen($query)){
            $result .= '?' . $query;
        }

        return $result;
    }

    protected function getBasePart($paramPart)
    {
        $rootId = (int) Mage::app()->getStore()->getRootCategoryId();
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key');
        $seoAttributePartExist = strlen($paramPart) && strpos($paramPart, '?') !== 0;

        $base = Mage::getBaseUrl();

        if ($this->isCatalogSearch()){
            $url = $base . 'catalogsearch/result/';
        }
        elseif ($this->isNewOrSale()) {
            $url = $base . $this->moduleName;
        }
        elseif ($this->getCurrentLandingKey()) {
            $url = $base . $this->getCurrentLandingKey();

            if ($seoAttributePartExist) {
                $url.= '/';
            } else {
                $url = $this->getUrlHelper()->checkAddSuffix($url);
            }
        }
        elseif ($this->isCategorySearch()) {
            $url = $base . 'categorysearch/categorysearch/search/';
        }
        elseif (Mage::app()->getRequest()->getControllerModule() == 'Mage_Cms') { // homepage,
            $hasFilter = false;
            if (Mage::getStoreConfig('amshopby/block/ajax')) {
                $hasFilter = true;
            }
            if (!$hasFilter) {
                foreach (array_keys($this->query) as $k){
                    if (!in_array($k, array('p','mode','order','dir','limit')) && false === strpos('__', $k)){
                        $hasFilter = true;
                        break;
                    }
                }
            }

            // homepage filter links
            if ($this->isUrlKeyMode() && $hasFilter){
                $url = $base . $reservedKey . '/';
            }
            // homepage sorting/paging url
            else {
                $url = $base;
            }
        }
        elseif ($this->getCategoryId() == $rootId) {
            $url = $base;

            switch ($this->mode) {
                case Amasty_Shopby_Model_Source_Url_Mode::MODE_DISABLED:
                    $needUrlKey = true;
                    break;
                case Amasty_Shopby_Model_Source_Url_Mode::MODE_MULTILEVEL:
                    $needUrlKey = !$this->isBrandPage();
                    break;
                case Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT:
                    $needUrlKey = !$seoAttributePartExist;
                    break;
                default:
                    $needUrlKey = true;
            }
            if ($needUrlKey) {
                $url.= $reservedKey;
                if ($seoAttributePartExist) {
                    $url .=  '/';
                }
            }
        }
        else { // we have a valid category
            $url = $this->getCategoryObject()->getUrl();
            $pos = strpos($url,'?');
            $url = $pos ? substr($url, 0, $pos) : $url;

            if ($seoAttributePartExist) {
                $url = $this->getUrlHelper()->checkRemoveSuffix($url);
                if ($this->isUrlKeyMode()) {
                    $url .= '/' . $reservedKey;
                }
                $url.= '/';
            }

        }

        return $url;
    }

    protected function isBrandPage()
    {
        if ($this->getCurrentLandingKey()) {
            return false;
        }

        $attrCode = trim(Mage::getStoreConfig('amshopby/brands/attr'));
        if (!$attrCode) {
            return false;
        }

        if ($this->getCategoryId() != (int) Mage::app()->getStore()->getRootCategoryId()) {
            return false;
        }

        if (empty($this->effectiveQuery[$attrCode])) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    protected function getCategoryId()
    {
        return is_object($this->category) ? $this->category->getId() : $this->category;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    protected function getCategoryObject()
    {
        if (!is_object($this->category)) {
            $this->category = Mage::getModel('catalog/category')->load($this->category);
        }
        return $this->category;
    }

    protected function isNewOrSale()
    {
        return in_array($this->moduleName, array('catalognew', 'catalogsale'));
    }

    protected function isSomeSearch()
    {
        return $this->isCatalogSearch() || $this->isCategorySearch();
    }

    protected function isCatalogSearch()
    {
        return in_array($this->moduleName, array('sqli_singlesearchresult', 'catalogsearch'));
    }

    protected function isCategorySearch()
    {
        return $this->moduleName == 'categorysearch';
    }

    protected function getCurrentLandingKey()
    {
        return Mage::app()->getRequest()->getParam('am_landing');
    }

    protected function getUrlHelper()
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        return $helper;
    }

    protected function isUrlKeyMode()
    {
        return $this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_MULTILEVEL || $this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_DISABLED;
    }
}
