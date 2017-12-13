<?php

class ObjectSource_Carttransfer_IndexController extends Mage_Core_Controller_Front_Action
{

  public function indexAction()
  {
      $params = $this->getRequest()->getParams();
      
      if (empty($params['qid']) || empty($params['pid']))
      {
        $this->_redirect('/');
        return;
      }
      
      $this->loadLayout();
      $layout = $this->getlayout();
      
      if ($layout->getBlock('oscarttransfer_content'))
      {
        $layout->getBlock('oscarttransfer_content')->setQid($params['qid']);
        $layout->getBlock('oscarttransfer_content')->setPid($params['pid']);
      }
      
      $this->renderLayout();
      
      return;
  }
  
  public function addAction()
  {
  
      $params = $this->getRequest()->getParams();
      if ($params['pid'])
      {
        $redirectProduct = Mage::getModel('catalog/product')->load($params['pid']);
        $redirectProductUrl = $redirectProduct->getUrlPath();
        
        // TODO: Remove hardcoded store link
        $store = Mage::getSingleton('core/store')->load(2);
        
        // TODO: Add better error handling when attributes are not passed
        $quote = Mage::getModel('sales/quote')->setStore($store)->load($params['qid']);
        
        // Load the cart
        $cart = Mage::getModel('checkout/cart');
        
        // Get the products that can be added to the cart
        // Only products that are in the website and visible
        foreach($quote->getAllItems() as $quoteItem)
        {
            
            $product = $quoteItem->getProduct();
            
            if ($product->setStoreId(1)->isVisibleInSiteVisibility())
            {
                // If the product is of a configurable type then build the params to add
                if ($product->getTypeId() === 'configurable')
                {
                  
                  $childProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $product->getSku());
                  $configAttributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                  
                  $attributes = array();
                  foreach($configAttributes as $attribute)
                  {
                    $attributes[$attribute->getAttributeId()] = $childProduct->getData($attribute->getProductAttribute()->getData('attribute_code'));
                  }
                  
                  $productParams = array(
                    'product' => $product->getId(),
                    'super_attribute' => $attributes,
                    'qty' => $quoteItem->getQty(),
                  );               
                                    
                                
                }
                else
                {
                  $productParams = null;
                }
                
                $cart->addProduct($quoteItem->getProduct(), $productParams);
            }
            
        }
        
        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        
        Mage::getSingleton('core/session')->addSuccess($this->__('You cart items were transferred'));
      
        if (!empty($redirectProductUrl))
        {
          $this->_redirect($redirectProductUrl);
        } 
        else
        {
          echo "product not found";
        }
      }
      
  }

}