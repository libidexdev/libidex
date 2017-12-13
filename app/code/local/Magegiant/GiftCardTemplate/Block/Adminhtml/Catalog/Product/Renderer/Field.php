<?php

/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Catalog_Product_Renderer_Field extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $_htmlId = $this->getHtmlId();
        if ($this->getEntityAttribute()->getFrontend()->getInputType() == 'select') {
            $html = '<select id="giftcard_' . $_htmlId . '" name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . '>' . "\n";
            foreach ($this->getValues() as $option) {
                $html .= '<option value="' . $this->_escape($option['value']) . '"';
                $html .= isset($option['title']) ? 'title="' . $this->_escape($option['title']) . '"' : '';
                $html .= isset($option['style']) ? 'style="' . $option['style'] . '"' : '';
                if ($option['value'] == $this->getFieldValue($_htmlId)) {
                    $html .= ' selected="selected"';
                }
                $html .= '>' . $this->_escape($option['label']) . '</option>' . "\n";
            }
            $html .= '</select>';
        } else {
            $required = $this->getRequired() ? 'required-entry' : '';
            $html     = '<input type="text" class="input-text ' . $required . '" id="giftcard_' . $_htmlId . '" name="' . $this->getName() . '" value="' . $this->getFieldValue($_htmlId) . '"/>';
        }
        $html .= '<script type="text/javascript">
					Event.observe(window, "load", function() {
                        var templateEl=$("giftcard_' . $_htmlId . '");
                        var seeMoreEl=$("see_more_design");
                        changeDesign();
					    templateEl.observe("change",function(){
					        changeDesign();
					    })
						function changeDesign(){
                            if(templateEl.value==-1){
                                seeMoreEl.up("tr").hide();
                            }
                            else{
                                seeMoreEl.up("tr").show();
                            }
						}
					})
				  </script>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    public function getName($name = null)
    {
        if (is_null($name))
            $name = $this->getData('name');

        if ($suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        }

        return $name;
    }

    public function isNew()
    {
        if (Mage::registry('product')->getId()) {
            return false;
        }

        return true;
    }

    public function getFieldValue($field)
    {
        if (!$this->isNew()) {
            return Mage::registry('product')->getDataUsingMethod($field);
        }

        return $this->getConfigValue($field);
    }

    public function getConfigValue($field)
    {
        $group = Mage::helper('giftcard')->getConfig('map/' . $field);
        if (!$group) $group = 'general';

        return Mage::helper('giftcard')->getConfig($group . '/' . $field);
    }
}
