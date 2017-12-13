<?php
class ObjectSource_Manufacturer_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    public function getManufacturerName()
    {
        return Mage::getModel('dropcommon/dropship')->load($this->getData('warehouse'))->getTitle();
    }

    public function getManufacturerProductClassName()
    {
        return 'product'. $this->getManufacturerName();
    }
}
