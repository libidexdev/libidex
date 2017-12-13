<?php

class ObjectSource_Configupsell_Block_Upsell extends Mage_Catalog_Block_Product_List_Upsell
{

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */
        $this->_itemCollection = $product->getUpSellProductCollection()
            ->setPositionOrder()
        ;

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', array(
            'product'       => $product,
            'collection'    => $this->_itemCollection,
            'limit'         => $this->getItemLimit()
        ));

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * Gets the configurable options template if needed
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getConfigurableHtml(Mage_Catalog_Model_Product $product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return Mage::app()->getLayout()
                ->createBlock('osconfigupsell/catalog_product_view_type_configurable')
                ->setProduct($product)
                ->setTemplate('osconfigupsell/catalog/product/list/configurable.phtml')
                ->toHtml();
        }
        return '';
    }
    
    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
    
    /**
     * Retrieve url to transfer over to Libidex
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getTransferProductUrl($product)
    {
        $url = $product->getProductUrl();
        $cart = Mage::getModel('checkout/cart')->getQuote();
        if ($cart && count($cart->getAllItems()) > 0)
        {
            $url = Mage::getUrl('oscarttransfer', array( 'pid' => $product->getId(), 'qid' => $cart->getId(), '_store' => 1 ));
        }
        return $url;
    }

}