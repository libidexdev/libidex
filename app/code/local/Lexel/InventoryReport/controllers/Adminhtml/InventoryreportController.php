<?php
class Lexel_InventoryReport_Adminhtml_InventoryreportController extends Mage_Adminhtml_Controller_Action {
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray) {
        return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName);
    }

    public function indexAction() {
        $this->loadLayout();
        $this->_setActiveMenu('inventorymenu');
        $this->_addContent($this->getLayout()->createBlock('lexel_inventoryreport/adminhtml_grid'));
        $this->renderLayout();
    }

    public function exportCsvAction() {
        $filename = 'inventory_report-' . date('d-m-Y') . '_' . date('H:i:s') . '.csv';
        $grid     = $this->getLayout()->createBlock('lexel_inventoryreport/adminhtml_grid');

        //$grid->setDefaultLimit(3415);

        return $this->_prepareDownloadResponse($filename, $grid->getCsvFile());
    }

    public function exportCsvEAction() {
        $filename = 'inventory_report-' . date('d-m-Y') . '_' . date('H:i:s') . '.csv';
        $grid     = $this->getLayout()->createBlock('lexel_inventoryreport/adminhtml_grid');

        return $this->_prepareDownloadResponse($filename, $grid->getCsvFileE());
    }
}