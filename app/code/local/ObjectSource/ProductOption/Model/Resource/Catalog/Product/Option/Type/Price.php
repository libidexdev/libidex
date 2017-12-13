<?php
/**
 * Object Source
 *
 * @copyright   Copyright (c) 2015 Object Source.
 **/
class ObjectSource_ProductOption_Model_Resource_Catalog_Product_Option_Type_Price
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_option_type_price', 'option_type_price_id');
    }
}