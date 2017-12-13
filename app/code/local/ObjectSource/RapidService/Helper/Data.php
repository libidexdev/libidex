<?php
class ObjectSource_RapidService_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Checks whether the current order should include Rapid Service surcharge.
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isRapidService(Mage_Sales_Model_Quote $quote)
    {
        // below amended to check for existence of virtual product not coupon code
        $visibleItems = $quote->getAllVisibleItems();

        foreach($visibleItems as $item) {

            if($item->getData('sku') == 'rapidSilver') {
                return true;
            }
        }

        return false;
    }

    /**
     * returns true if rapid service enabled else false
     * @return bool
     */
    public function isRapidServiceEnabled() {

        $test = Mage::getStoreConfig('rapidservice/general/enable');
        return (Mage::getStoreConfig('rapidservice/general/enable')) ? true : false;
    }


    /**
     * Checks whether all products in the cart are available for Rapid Order.
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isRapidServiceAvailable(Mage_Sales_Model_Quote $quote)
    {
        //if (!Mage::getStoreConfig('rapidservice/general/enable')) {
        //    return false;
        //}

        $visibleItems = $quote->getAllVisibleItems();

        foreach ($visibleItems as $_item) {
            $osRapidServiceEnabled = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue(
                    $_item->getProduct()->getId(), 'os_rapidservice', $quote->getStore()->getId()
                );

            // @todo amend below to remove virtual product and ignore coupon codes??
            /*
            if (!$osRapidServiceEnabled) {
                if ($this->isRapidService($quote)) {
                    $quote->collectTotals()
                        ->save();
                }

                return false;
            }
            */
            // If there's at least one eligible item in the cart, enable the service
            if($osRapidServiceEnabled) {
                return true;
            }
        }

        if ($this->isRapidService($quote)) {
            $quote->collectTotals()
                ->save();
        }

        return false;
    }

    /**
     * @param $item
     * @return bool
     * returns true if attribute set of product is 'Delivery'
     */
    public function isRapidServiceItem ($item)
    {
        $attributeSetName = '';
        $product = $item->getProduct();

        if($product != null) {
            $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
            $attributeSetModel->load($product->getAttributeSetId());
            $attributeSetName = $attributeSetModel->getAttributeSetName();
        }

        return ($attributeSetName == 'Delivery') ? true : false;
    }

    /**
     * checks if a delivery product is in the cart and adjusts the quantity to subtract it
     * @param $items
     * @param $qty
     * @return mixed
     */
    public function checkCartQuantity ($items, $qty) {

        $cartQty = $qty;

        foreach ($items as $item) {

            if($this->isRapidServiceItem(($item))) {
                $cartQty -= 1;
            }
        }

        return $cartQty;
    }

    /**
     * checks if order contains a rapid silver delivery item
     * @param $orderId
     * @param bool $incrementId
     * @return bool
     */
    public function getIsRapidServiceItemByOrderId ($orderId, $incrementId = false) {

        $order = ($incrementId) ? Mage::getModel("sales/order")->load($orderId, 'increment_id') : Mage::getModel("sales/order")->load($orderId);
        $orderItems = $order->getAllItems();

        foreach ($orderItems as $orderItem) {

            $isRapidService = $this->isRapidServiceItem($orderItem);

            if($isRapidService == true) {
                return true;
            }
        }

        return false;
    }
}
