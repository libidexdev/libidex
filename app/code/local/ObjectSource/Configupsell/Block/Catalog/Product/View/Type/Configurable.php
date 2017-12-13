<?php

class ObjectSource_Configupsell_Block_Catalog_Product_View_Type_Configurable
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = Mage::helper('core')->jsonDecode(parent::getJsonConfig());
        $config['containerId'] = 'configurable-container-' . $this->getProduct()->getId();
        return Mage::helper('core')->jsonEncode($config);
    }

}