<?php
class Lexel_LogProductSave_Model_Observer
{
    /**
     * Log product image attribute information each time a product is saved.
     * This is to help detect random missing images from products (LMSD-1818)
     *
     * @param $observer
     */
    public function logProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getId()) {
            Mage::log(
                'Saved Product ID: ' . $product->getId() . '. Product name: ' . $product->getName(),
                null,
                'Lexel_LogProductSave.log',
                true
            );

            $attributesToCheck = array('image', 'small_image', 'thumbnail');

            foreach ($attributesToCheck as $attribute) {
                $isChanged = $product->dataHasChangedFor($attribute);
                if ($isChanged) {
                    $originalValue = $product->getOrigData($attribute);
                    $newValue = $product->getData($attribute);

                    $text = "Attribute {$attribute} has changed" . PHP_EOL;
                    $text .= "Old value: {$originalValue}" . PHP_EOL;
                    $text .= "New value: {$newValue}" . PHP_EOL;

                    Mage::log($text, null, 'Lexel_LogProductSave.log', true);
                }
            }
        }
    }
}