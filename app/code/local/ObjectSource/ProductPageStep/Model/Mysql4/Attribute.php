<?php
class ObjectSource_ProductPageStep_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('productpagestep/attribute', 'attribute_id');
    }

    public function loadByOptionLabel($optionLabel)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('option_label=:option_label');

        $binds = array(
            'option_label' => $optionLabel
        );

        return $adapter->fetchRow($select, $binds);
    }
}