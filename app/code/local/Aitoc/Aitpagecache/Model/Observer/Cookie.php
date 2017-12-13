<?php

class Aitoc_Aitpagecache_Model_Observer_Cookie extends Aitoc_Aitpagecache_Model_Observer
{
    public function setAdminCookie($observer) {
        if(false == $this->_helper()->isEnabledForAdmin() || ($observer->getField()=='enable_for_admin' && $observer->getValue()==0)) {
            $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::ADMIN_PAGE_CACHE, Aitoc_Aitpagecache_Mainpage::ADMIN_PAGE_CACHE);
        }
        return $this;
    }

    public function deleteAdminCookie($observer) {
        $this->_helper()->delCacheCookie(Aitoc_Aitpagecache_Mainpage::ADMIN_PAGE_CACHE);
    }

    public function addNoBoosterCookie($observer) {
        //observer to disable booster after quote is converted to order to prevent caching some payment pages, used until success pages is opened or other any allowed booster pageopened
        $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID,Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID);
    }

    public function checkNoBoosterCookie($observer) {
        if(!isset($_COOKIE[Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID])) {
            return false;
        }
        $moduleName = $observer->getControllerAction()->getRequest()->getModuleName();
        //checking that we are still on checkout
        if(in_array($moduleName, array('checkout', 'paypal', 'twocheckout'/*paypall redirect*/, 'sagepaysuite', 'paypaluk', 'paygate')) ) { // update from config
            return false;
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if($quote != null) {
            $observer->setQuote($quote);
            $this->recalculateQuoteItems($observer);
        }
    }

    public function set_cache_cookie($observer = false) {
        $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::DEFAULT_COOKIE_ID, Aitoc_Aitpagecache_Mainpage::DEFAULT_COOKIE_ID);
    }

    public function onControllerActionPredispatchAddCookie($observer)
    {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            if(false == $this->_helper()->isEnabledForLogined()) {
                $this->set_cache_cookie();
                return ;
            }
        }
        $moduleName = $observer->getControllerAction()->getRequest()->getModuleName();
        $controllerName = $observer->getControllerAction()->getRequest()->getControllerName();
        $actionName = $observer->getControllerAction()->getRequest()->getActionName();
        /**
         * set cookie on any dynamic action (add to wishlist, add to compare, newsletter, poll)
         */
        if($moduleName == 'poll' && $actionName == 'add') {
            $this->set_cache_cookie();
        }
        else if($moduleName == 'newsletter' && $actionName == 'new') {
            $this->set_cache_cookie();
        }
        else if($moduleName == 'directory' && $controllerName = 'currency' && $actionName == 'switch') {
            $this->set_cache_cookie();
        }
        return $this;
    }

    public function onCustomerLoginSetCookie($observer) {
        if($this->_helper()->isEnabledForQuote()) {
            //checking amount of items in cart and setting cookie is needed
            $this->_helper()->checkQuoteItems('');
        }
        return $this;
    }

    public function onCustomerLogoutSetCookie($observer) {
        $this->_helper()->delCacheCookie(Aitoc_Aitpagecache_Mainpage::NOT_EMPTY_CART)
            ->delCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CART_ID);

        //for magento 1.6+ persistent cart
        if(isset($_COOKIE[Aitoc_Aitpagecache_Mainpage::PERSISTENT_COOKIE_ID]) && $_COOKIE[Aitoc_Aitpagecache_Mainpage::PERSISTENT_COOKIE_ID])
        {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $amount = $this->_helper()->countQuoteItems($quote);
            if($amount > 0)
            {
                $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CART_ID, $amount);
            }
        }

        return $this;
    }
}