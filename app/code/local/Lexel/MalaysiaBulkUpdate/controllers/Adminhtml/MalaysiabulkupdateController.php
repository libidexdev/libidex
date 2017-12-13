<?php
class Lexel_MalaysiaBulkUpdate_Adminhtml_MalaysiabulkupdateController extends Mage_Adminhtml_Controller_Action {

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('inventorymenu');
        $this->renderLayout();
    }

    public function exportCsvAction()
    {
        $path = Mage::getBaseDir('var') . DS . 'export' . DS . 'lexel_malaysia_price_export' . DS;
        $file =  $path . 'malaysia-prices-' . date('d-m-Y') . '_' . date('H:i:s') . '.csv';

        $csv = Mage::getModel('lexel_malaysiabulkupdate/prices');
        $csv->generateCSV($file);

        $this->_prepareDownloadResponse($file, array('type' => 'filename', 'value' => $file));
    }

    public function updateAction()
    {
        $factor = (float)$this->getRequest()->getPost('factor');
        $ready = $this->getRequest()->getPost('ready');
        if ($factor > 0) {
            Mage::getConfig()->saveConfig('lexel/malaysiabulkupdate/factor', $factor);
            Mage::getConfig()->saveConfig('lexel/malaysiabulkupdate/ready', $ready);
            Mage::getSingleton('core/session')->addNotice('When the next update job is scheduled to run, if the job is enabled it will multiply existing prices by ' . $factor);
        } else {
            Mage::getConfig()->saveConfig('lexel/malaysiabulkupdate/ready', 0);
            Mage::getSingleton('core/session')->addNotice('An invalid factor or 0 was entered so the job has been disabled, please resolve before trying again');
        }

        // Clear config cache
        Mage::app()->getStore()->resetConfig();

        $this->_redirect('adminhtml/malaysiabulkupdate/');
    }
}
