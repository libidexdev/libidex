<?php
include 'app/Mage.php';
if (isset($_GET['factor'])) {
    $factor = $_GET['factor'];
} else {
    $factor = false;
}

Mage::app();

$products = Mage::getModel('catalog/product')
    ->getCollection()
    ->addAttributeToSelect('malaysia_price');

foreach ($products as $product) {
    error_log('Processing ' . $product->getEntityId());
    $price = $product->getMalaysiaPrice();
    $newPrice = $price * $factor;
    echo $product->getEntityId() . ' > WAS: ' . $price . ' NOW: ' . $newPrice;
    echo '<br />';
    if ($factor != false) {
        $product->setMalaysiaPrice($newPrice);
        error_log('Saving');
        $product->save();
        error_log('Finished saving');
    }
}