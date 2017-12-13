<?php
class ObjectSource_ProductPageStep_Block_Configurableattribute extends Mage_Core_Block_Template
{
    private $_option = null;

    public function getOptionHtml($option)
    {
        return $this->toHtml();
    }

    public function setOption($option)
    {
        $this->_option = $option;
    }

    public function getOption()
    {
        return $this->_option;
    }
}