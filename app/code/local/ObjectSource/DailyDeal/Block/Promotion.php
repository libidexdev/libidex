<?php
class ObjectSource_DailyDeal_Block_Promotion extends Mage_Core_Block_Template
{
    private $_promotion = null;

    public function getStaticBlock()
    {
        if (!empty($this->_promotion))
        {
            $product = $this->_promotion->getDailydealPromotionProduct();
            if (empty($product)) {
                return 'Product SKU no longer exists';
            }

            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load('promotion_for_daily_deal');

            $array = array();
            $array['product_name'] = $product->getName();
            $array['product_url'] = $product->getProductUrl();
            $array['product_price'] = Mage::helper('core')->currency($product->getPrice(), true, false);
            $array['product_discount_price'] = Mage::helper('core')->currency($product->getFinalPrice(), true, false);
            $array['product_image_small'] = Mage::helper('catalog/image')->init($product, 'small_image')->
                constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(576,null);
            //$product->getImageUrl();
            $discount = $this->_promotion->getDiscountAmount();
            $array['discount_amount'] = ($discount - intval($discount) > .0 ?
                number_format($discount, 2, '.', ' ') : intval($discount));

            $filter = Mage::getModel('cms/template_filter');
            $filter->setVariables($array);

            return $filter->filter($block->getContent());
        }

        return 'No active promotion';
    }

    public function setPromotion($promotion)
    {
        $this->_promotion = $promotion;

        return $this;
    }

    public function getPromotion()
    {
        return $this->_promotion;
    }
}
