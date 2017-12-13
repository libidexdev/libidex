<?php
class Lexel_MalaysiaBulkUpdate_Model_Prices extends Mage_Core_Model_Abstract
{
    public function generateCSV($file)
    {
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('malaysia_price');
            //->addAttributeToFilter('sku', array('nlike' => 'LEX-%'));

        $outputArray = [];

        $itemRow = [];
        $itemRow['sku'] = 'Product SKU';
        $itemRow['price'] = 'Original Price';
        $itemRow['malaysia_price'] = 'Malaysia Price';
        $outputArray[] = $itemRow;

        foreach ($products as $product) {
            $itemRow = [];
            $itemRow['sku'] = $product->getSku();
            $itemRow['price'] = $product->getPrice();
            // if it needs to be as currency:
            // $itemRow['malaysia_price'] = Mage::helper('core')->currency($product->getMalaysiaPrice(), true, false);
            $itemRow['malaysia_price'] = $product->getMalaysiaPrice();
            $outputArray[] = $itemRow;
        }

        $outputCsv = new Varien_File_Csv();
        $outputCsv->saveData($file, $outputArray);
    }
}