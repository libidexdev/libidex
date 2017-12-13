<?php
class Aitoc_Aitpagecache_Block_Rewrite_TagProductList extends Mage_Tag_Block_Product_List
{   
    public function _toHtml()
    {
       // echo "<pre>"; 
       $html = parent::_toHtml();
       $html = preg_replace("/<script[^>]+>.+<\/script>/ismU", "", $html);
       return $html;
    }
}
