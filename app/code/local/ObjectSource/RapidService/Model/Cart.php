<?php
/**
 * Created by PhpStorm.
 * User: kieron
 * Date: 02/09/16
 * Time: 16:48
 */
class ObjectSource_RapidService_Model_Cart extends Mage_Core_Model_Abstract {

    /**
     * adds rapid silver product to cart returns updated prices and product values
     * @return array
     */
    public function addToQuote() {

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $cart = Mage::getSingleton("checkout/cart");

        $productModel = Mage::getModel("catalog/product");
        $productId = (int)$productModel->getIdBySku('rapidSilver');
        $product = $productModel->load($productId);
        $options = new Varien_Object(array("product" => $productId, "qty" => 1 ));

        $quote->addProduct($product, $options);

        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $quote->collectTotals()->save();
        $cart->save();

        $totals = $quote->getTotals();
        $percentageTotal = $totals['os_rapidservice']->getData('value');

        $response = array();
        $response['data']['message'] = "success";
        $response['data']['name'] = $product->getData('name');
        $response['data']['percentage'] = Mage::getStoreConfig('rapidservice/general/surcharge');
        $response['data']['percentageTotal'] = number_format($percentageTotal, 2);
        $response['data']['total'] = number_format($quote->getBaseGrandTotal(), 2);

        return $response;
    }

    /**
     * removes silver rapid product from cart and returns new total price
     * @return mixed
     */
    public function removeFromQuote() {

        $cart = Mage::getSingleton('checkout/session');
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cart->getQuote()->getAllItems();

        foreach ($items as $item) {
            if($item->getData('sku') == 'rapidSilver') {
                try {
                    $cartHelper->getCart()->removeItem($item->getItemId());
                    $cartHelper->getCart()->save();
                }
                catch ( Exception $e ) {
                    Mage::log('Cart Error: Can\'t remove cart item by product id: ' . $e->getMessage(), null, 'cart.log');
                }
            }
        }

        $cart->setCartWasUpdated(true);
        $quote = $cart->getQuote()->collectTotals()->save();

        $total['total'] = number_format($quote->getBaseGrandTotal(), 2);

        return $total;
    }
}