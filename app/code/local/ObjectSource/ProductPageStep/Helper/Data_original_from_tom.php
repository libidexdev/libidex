<?php
class ObjectSource_ProductPageStep_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_selectorIncrementId = 80;
    private $_tabIds = array();
    private $_optionsBlock;

    public function getSelectASwatchImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/swatches/color-main/preview/select-colour.jpg';
    }

    public function getNestedOptions($options)
    {
        // Produce an array of attribute details with option
        $attributeOptions = array();

        foreach ($options as $option)
        {
            if (get_class($option) == 'Mage_Catalog_Model_Product_Type_Configurable_Attribute')
                $optionLabel = $option->getLabel();
            else
                $optionLabel = $option->getTitle();
            $attributeModel = Mage::getModel('productpagestep/attribute')->loadByOptionLabel($optionLabel);
            $attributeId = $attributeModel->getAttributeId();
            if (!empty($attributeId))
            {
                $attributeData = $attributeModel->getData();
                $attributeData['_option'] = $option;
                $attributeOptions[] = $attributeData;
            }
        }

        // Produce an array in nested structure for rendering
        $nestedOptions = array();

        foreach ($attributeOptions as $attributeOption)
        {
            $categories = explode('>', $attributeOption['category']);

            /* // Method 1: References
            $countCategories = count($categories);
            $nextLevel = &$nestedOptions;
            foreach ($categories as $i => $category)
            {
                if ($i <= ($countCategories))
                {
                    $nextLevel[$category] = $attributeOption['_option'];
                }
                else
                {
                    $nextLevel[$category] = array();
                }

                $nextLevel = &$nextLevel[$category];
            }*/

            // Method 2: Eval
            $dimensions = '';
            foreach ($categories as $category) {
                $dimensions .= "['" . addslashes($category) . "']";
            }
            eval('$nestedOptions'.$dimensions.'=array("option" => $attributeOption["_option"], "class" => $attributeOption["class"]);');
        }

        return $nestedOptions;
    }

    public function getProductPageStepHtml($options)
    {
        $nestedOptions =  $this->getNestedOptions($options);


        if (!count($nestedOptions))
            return '';

        $html = $this->getProductPageStepLevelHtml($nestedOptions, 1);

        $html .= '<div id="nextStepBtn" class="button">Next</div>';
        $html .= '<div style="clear:both"></div>';
        $html .= '<script type="text/javascript">jQuery("'.implode(',',$this->_tabIds).'").tabs();</script>';
        return $html;
    }

    protected function getProductPageStepLevelHtml($nestedOptions, $initial=0)
    {
        $html = '';
        if (!isset($nestedOptions['option']))
        {
            $this->_selectorIncrementId += 1;
            $html .= '<div id="tab_'.$this->_selectorIncrementId.'">';
            $this->_tabIds[] = '#tab_'.$this->_selectorIncrementId;

            $navItems = array_keys($nestedOptions);
            $html .= '<ul>';
            $incList = array();
            foreach ($navItems as $navitem)
            {
                $this->_selectorIncrementId += 1;
                $incList[] = $this->_selectorIncrementId;
                $html .= '<li><a href="#item_'.$this->_selectorIncrementId.'">'.$navitem.'</a></li>';
            }
            $html .= '</ul>';

            foreach ($navItems as $i => $navitem)
            {
                $html .= '<div id="item_'.$incList[$i].'" class="tabPanel">';
                $html .= $this->getProductPageStepLevelHtml($nestedOptions[$navitem]);
                $html .= '</div>';
            }

            $html .= '</div>';
        }
        else
        {
            $this->_selectorIncrementId += 1;
            if (get_class($nestedOptions['option']) == 'Mage_Catalog_Model_Product_Type_Configurable_Attribute')
                $this->_optionsBlock->setOption($nestedOptions['option']);
            Mage::register('productpagestep_class', $nestedOptions['class']);
            $html .= $this->_optionsBlock->getOptionHtml($nestedOptions['option']);
            Mage::unregister('productpagestep_class');
        }

        return $html;
    }

    public function setOptionsBlock($optionsBlock)
    {
        $this->_optionsBlock = $optionsBlock;
    }

    public function getOptionsBlock()
    {
        return $this->_optionsBlock;
    }
}