<?php
class Aitoc_Aitpagecache_Model_Target_Data extends Mage_Core_Model_Abstract
{
    // array of unique product Ids from loaded collections
    protected $productPageData = array();

    public function getProductPageData()
    {
        return $this->productPageData;
    }

    public function addProductPageData($id)
    {
        if($id && !in_array($id, $this->productPageData))
        {
            $this->productPageData[] = $id;
        }
    }
}