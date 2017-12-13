<?php

class Magegiant_GiftCardTemplate_Block_Adminhtml_Button_Import_Sample extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $el)
    {
        $url  = $this->getUrl('adminhtml/giftcard_template_import/sample');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('import-sample')
            ->setLabel($this->__('Import Sample Data'))
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}
