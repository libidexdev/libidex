<?php
/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

class Magegiant_GiftCard_Adminhtml_Giftcard_HistoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magegiant_GiftCard_Adminhtml_GiftcardController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('giftcard/history')
            ->_addBreadcrumb(
                $this->__('Gift Card History'),
                $this->__('Gift Card History')
            );
        $this->_title($this->__('Gift Cart'))->_title($this->__('History'));

        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'giftcard_history.csv';
        $content  = $this->getLayout()
            ->createBlock('giftcard/adminhtml_history_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'giftcard_history.xml';
        $content  = $this->getLayout()
            ->createBlock('giftcard/adminhtml_history_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _construct()
    {
        $this->setUsedModuleName('Magegiant_GiftCard');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftcard/history');
    }
}