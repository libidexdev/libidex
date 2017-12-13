<?php
class ObjectSource_ProductPageStep_Model_Attribute extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('productpagestep/attribute');
    }

    public function loadByOptionLabel($optionLabel)
    {
        $this->setData($this->getResource()->loadByOptionLabel($optionLabel));
        return $this;
    }

    public function getOptionClassArray()
    {
        $collection = $this->getCollection()
            ->addFieldToSelect(array('option_label','class'));

        $newArray = array();
        foreach ($collection as $item)
        {
            $newArray[$item->getOptionLabel()] = $item->getClass();
        }
        return $newArray;
    }
}
