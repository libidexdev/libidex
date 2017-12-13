<?php
class ObjectSource_MalaysiaInvoice_Adminhtml_MalaysiainvoiceController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        //$this->_setActiveMenu('sales/malaysia_invoice');

        $this->_title($this->__('Sales'))
            ->_title($this->__('Malaysia Invoices'));

        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('objectsource/malaysia_invoice');
        return true;
    }

    public function newAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');

        if (!is_array($orderIds) || empty($orderIds)) {
            $this->_redirect('adminhtml/sales_order');
        }

        $invoice = $this->_initInvoice();
        $invoice->setOrderIds($orderIds);

        $this->loadLayout();
        $invoiceForm = $this->getLayout()->getBlock('invoice_edit');
        $invoiceTabForm = $this->getLayout()->getBlock('invoice_tabs');

        if (!$invoiceForm || !$invoiceForm instanceof Mage_Core_Block_Abstract) {
            $this->_redirect('adminhtml/sales_order');
        }

        $invoiceForm->setOrderIds($orderIds)->setFormAction($this->getUrl('*/*/save'));
        $invoiceTabForm->setOrderIds($orderIds);

        $this->renderLayout();
    }

    public function saveAction()
    {
        $invoiceReference = $this->getRequest()->getParam('invoice_reference');
        $awbNumber = $this->getRequest()->getParam('awb_number');
        $exchangeRate = (float)$this->getRequest()->getParam('exchange_rate_gbp_usd');
        $malaysiaTotalUsd = (float)$this->getRequest()->getParam('malaysia_total_usd');
        $orderIds = $this->getRequest()->getParam('order_ids');

        if (!$invoiceReference || !$awbNumber) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('os_malaysia_invoice')->__('Some required data are missing')
            );
            $this->_redirect('*/*/new', array('order_ids' => $orderIds));
        }

        if (!$exchangeRate) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('os_malaysia_invoice')->__('Excahnge rate should be a number (i.e. 1.65)')
            );
            $this->_redirect('*/*/new', array('order_ids' => $orderIds));
        }

        /** @var ObjectSource_MalaysiaInvoice_Model_Invoice $invoice */
        $invoice = Mage::getModel('os_malaysia_invoice/invoice')
            ->setInvoiceReference($invoiceReference)
            ->setAwbNumber($awbNumber)
            ->setExchangeRateGbpUsd($exchangeRate)
            ->setOrderIds($orderIds)
            ->setMalaysiaTotalUsd($malaysiaTotalUsd)
            ->setCreatedAt(Varien_Date::now())
            ->setUpdatedAt(Varien_Date::now());

        try {
            $invoice->getResource()->getReadConnection()->beginTransaction();
            $invoice->save();

            $itemIds = (array)$this->getRequest()->getParam('item_id');
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('entity_id', array('in' => explode(',', $orderIds)));

            $malaysiaTotalGbp = 0;

            foreach ($itemIds as $itemId) {
                list($realItemId) = explode('_', $itemId);
                $realItem = Mage::getModel('sales/order_item')->load($realItemId);
                $realItem = Mage::helper('os_malaysia_invoice/customOption')->decodeCustomOptions($realItem);
                $malaysiaPrice = Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue($realItem->getProductId(), 'malaysia_price', $realItem->getStoreId());
                $malaysiaTotalGbp += $malaysiaPrice;

                $item = Mage::getModel('os_malaysia_invoice/invoice_item')
                    ->setInvoiceId($invoice->getId())
                    ->setItemId($realItemId)
                    ->setMalaysiaPrice($malaysiaPrice)
                    ->setProductName($realItem->getName())
                    ->setIncrementOrderId($orders->getItemById($realItem->getOrderId())->getIncrementId())
                    ->setSku($realItem->getSku())
                    ->setColor($realItem->getColor())
                    ->setSize($realItem->getSize())
                    ->setCreatedAt(Varien_Date::now())
                    ->setUpdatedAt(Varien_Date::now());

                $comments = (array)$this->getRequest()->getParam('comment');
                if (array_key_exists($itemId, $comments)) {
                    $item->setComment($comments[$itemId]);
                }
                $item->save();
            }

            $invoice->setMalaysiaTotalGbp($malaysiaTotalGbp);

            if (!$malaysiaTotalUsd) {
                $malaysiaTotalUsd = $exchangeRate * $malaysiaTotalGbp;
                $invoice->setMalaysiaTotalUsd($malaysiaTotalUsd);
            }

            $invoice->save();
            $invoice->getResource()->getReadConnection()->commit();

        } catch (Exception $e) {
            $invoice->getResource()->getReadConnection()->rollBack();
            Mage::logException($e);
        }

        $this->_redirect('*/*/');
    }

    /**
     * Prepares invoice data.
     *
     * @return false|Mage_Core_Model_Abstract
     */
    protected function _initInvoice()
    {
        $this->_title($this->__('Sales'))
            ->_title($this->__('Malaysia Invoices'));

        $invoiceId  = (int) $this->getRequest()->getParam('invoice_id');
        $invoice    = Mage::getModel('os_malaysia_invoice/invoice');

        if (!$invoiceId) {
            if ($invoiceReference = $this->getRequest()->getParam('invoice_reference')) {
                $invoice->setInvoiceRefrerence($invoiceReference);
            }

            if ($usdValue = $this->getRequest()->getParam('malaysia_total_usd')) {
                $invoice->setMalaysiaTotalUsd($usdValue);
            }
        }

        $invoice->setData('_edit_mode', true);

        if ($invoiceId) {
            try {
                $invoice->load($invoiceId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        Mage::register('invoice', $invoice);
        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Display single invoice.
     */
    public function viewAction()
    {
        if (!($invoiceId = $this->getRequest()->getParam('invoice_id'))) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('os_malaysia_invoice')->__('Please select invoice to view')
            );
            $this->_redirect('*/*');
        }

        $invoice = Mage::getModel('os_malaysia_invoice/invoice')->load($invoiceId);

        if (!$invoice->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('os_malaysia_invoice')->__('Invlaid invoice id')
            );
            $this->_redirect('*/*');
        }

        Mage::register('current_invoice', $invoice);

        $this->loadLayout();
        $this->getLayout()->getBlock('invoice_view')->setInvoice($invoice);
        $this->renderLayout();
    }

    public function printInvoiceAction()
    {
        $this->_initInvoice();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function printPackingListAction()
    {
        $this->_initInvoice();
        $this->loadLayout();
        $this->renderLayout();
    }
}
