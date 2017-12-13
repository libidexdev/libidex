<?php

class ObjectSource_Carttransfer_Block_Index extends Mage_Core_Block_Template
{
  
  private $_redirectProduct = null;
  
  protected function getCartProducts()
  {
      $products = array();
      
      // TODO: Remove hardcoded store link
      $store = Mage::getSingleton('core/store')->load(2);
          
      // Load the quote object that was passed in the querystring
      $quote = Mage::getModel('sales/quote')->setStore($store)->load($this->getQid());
      
      // Get the products that can be added to the cart
      // Only products that are in the website and visible
      foreach($quote->getAllItems() as $quoteItem)
      {
          $product = $quoteItem->getProduct();

          // TODO: Remove hardcoded reference to the store ID
          if ($product->setStoreId(1)->isVisibleInSiteVisibility())
          {
              $products[] = $product;
          }
      }
      
      return $products;
  }
  
  private function _getRedirectProduct()
  {
    if ($_redirectProduct == null)
    {
      $_redirectProduct = Mage::getModel('catalog/product')->load($this->getPid());
    }
    else
    {
      $_redirectProduct = Mage::getModel('catalog/product');
    }
    return $_redirectProduct;
  }
  
  protected function getRedirectProductName()
  {
    return $this->_getRedirectProduct()->getName();
  }
  
  protected function getRedirectProductUrl()
  {
    return $this->_getRedirectProduct()->getUrlPath();
  }
  
  protected function getAddToCartUrl()
  {
    return Mage::getUrl('*/*/add', 
      array(
        'pid' => $this->getPid(),
        'qid' => $this->getQid(),
      )
    );
  }

}