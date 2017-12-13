<?php

class Aitoc_Aitpagecache_Adminhtml_AitpagecacheController extends Mage_Adminhtml_Controller_Action
{
    public function pendingAction()
    {
        #Mage::getModel('aitpagecache/observer_emails')->send();
        $this->loadLayout()
            ->_setActiveMenu('newsletter/aitpagecache')
            ->_addBreadcrumb(Mage::helper('aitpagecache')->__('Magento Booster Pending Emails'), Mage::helper('aitpagecache')->__('Magento Booster Pending Emails'));
        $this->_title(Mage::helper('aitpagecache')->__('Magento Booster'))->_title(Mage::helper('aitpagecache')->__('Pending Emails'));
        $this->_addContent($this->getLayout()->createBlock('aitpagecache/adminhtml_emailsPending'));
        $this->renderLayout();
    }

    public function sentAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('newsletter/aitpagecache')
            ->_addBreadcrumb(Mage::helper('aitpagecache')->__('Magento Booster Sent Emails'), Mage::helper('aitpagecache')->__('Magento Booster Sent Emails'));
        $this->_title(Mage::helper('aitpagecache')->__('Magento Booster'))->_title(Mage::helper('aitpagecache')->__('Sent Emails'));
        $this->_addContent($this->getLayout()->createBlock('aitpagecache/adminhtml_emailsSent'));
        $this->renderLayout();
    }

    public function warmupstartAction()
    {
        $helper = Mage::helper('aitpagecache/warmup');
        $collection = Mage::getResourceModel('core/url_rewrite_collection');
        $data = array(
            'isEnable' => 1,
            'position' => 0,
            'all' => $collection->getSize()
        );
        try
        {
            $helper->saveWarmupSetting($data);
            Mage::getSingleton('adminhtml/session')->addSuccess('Warm-up Magento Booster Cache is run');
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError('Can\'t run Warm-up Magento Booster Cache');
            Mage::log((string) $e);
        }

        $this->_redirect('*/cache/index');
    }
}