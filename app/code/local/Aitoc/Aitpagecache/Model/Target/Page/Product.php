<?php

class Aitoc_Aitpagecache_Model_Target_Page_Product extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('aitpagecache/target_page_product');
    }

    public function saveData($productIds, $pageId)
    {
        $products = array();
        foreach($productIds as $id)
        {
            $products[] = array('product_id'=>$id, 'page_id'=>$pageId);
        }

        $this->getResource()->saveTargetData($products);
    }
}