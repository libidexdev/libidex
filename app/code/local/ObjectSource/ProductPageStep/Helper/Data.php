<?php
class ObjectSource_ProductPageStep_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_selectorIncrementId = 80;
    private $_tabIds = array();
    private $_optionsBlock;

    public function getSelectASwatchImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/swatches/preview/select-colour-new.jpg';
    }
    public function getSelectAStyleImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/swatches/preview/select-style.jpg';
    }
    public function getSelectAOptionImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/swatches/preview/select-option.png';
    }

    public function getSelectASizeImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/swatches/preview/select-size.jpg';
    }
	
    public function getNestedOptions($options)
    {
        // Produce an array of attribute details with option
        $attributeOptions = array();

        $nestedOptions = array();
        foreach($options as $option)
        {
            if (get_class($option) == 'Mage_Catalog_Model_Product_Type_Configurable_Attribute')
                $optionLabel = $option->getLabel();
            else
                $optionLabel = $option->getTitle();
            $attributeModel = Mage::getModel('productpagestep/attribute')->loadByOptionLabel($optionLabel);
            $attributeId = $attributeModel->getAttributeId();
            unset($current);
            $current = null;

			$categories = explode('>', $attributeModel['category']);
			for($i = 0; $i < count($categories); $i++)
			{
				if ($current === null)
				{
					if (!array_key_exists($categories[$i], $nestedOptions))
					{
						$nestedOptions[$categories[$i]] = array();
					}
					$current =& $nestedOptions[$categories[$i]];
				}
				else
				{
					if (!array_key_exists($categories[$i], $current))
					{
						$current[$categories[$i]] = array();
					}
					$current =& $current[$categories[$i]];
				}
				if ($i === (count($categories)-1))
				{
					$current = array("option" => $option, "class" => $attributeModel["class"], "title2" => $attributeModel["title2"], "content2" => $attributeModel["content2"]);
				}
			}
		}

        return $nestedOptions;
    }

    public function getProductPageStepHtml($options)
    {
        $nestedOptions = $this->getNestedOptions($options);
        if (!count($nestedOptions)) return '';

        $html = '';
        $html .= <<<EOF
			<script type='text/javascript'>
				function addRestrictions(el, valueText, elType)
				{
					restriction = '';
					switch(valueText)
					{
						case 'Back':
							restriction = 'This option is incompatible with the Anal Sheath, Crotch Zip';
							break;
						case 'Front':
							restriction = 'This option is incompatible with the Crotch Zip, Full Face Hood, Ponytail Hood, Ponytail, Ponytail Hole, Open Hood, Cock-Balls Sheath, Crotch Hole & Anal Sheath';
							break;
						case 'Half Front':
							restriction = 'This option is incompatible with the Full Face Hood, Open Face Hood & Ponytail Hood';
							break;
						default:
							restriction = '';
							break;				
					}
					el.parentNode.childElements().grep(new Selector('.option-restriction')).each(function (e) {e.remove()});
					if (restriction != '')
					{
						if (elType == 'select')
						{
							el.parentNode.insert('<div class="option-restriction">' + restriction + '</div>');
						}
						else if (elType == 'radio')
						{
							el.parentNode.insert('<div class="option-restriction">' + restriction + '</div>');
						}
					}
				}
			</script>
EOF;
        $html .= '<div id="accordion1">';

		$summary = '';
		$summary = '<div id="product-summary-title"><h4 class="product-summary-title priority-1-heading thick">Your Order</h4></div>';
		$accordionLevel = -1;
		foreach($nestedOptions as $level1Key => $level1Value)
		{
			$accordionLevel++;
 			if (strpos($level1Key, "Choose your Extras (optional)") !== false)
			{
				$optional = 'summary-optional';
				$optionalTitle = "Extras (optional)";
			}
			else
			{
				$optional = '';
				switch ($level1Key)
				{
					case 'Choose Your Colour':
						$optionalTitle = 'Colour';
						break;
					case 'Choose Your Size':
						$optionalTitle = 'Size';
						break;
					case 'Choose Your Style (e.g. Zips, Feet etc)':
						$optionalTitle = 'style';
						break;
					default:
						$optionalTitle = $level1Key;
						break;
				}
			}
			// For Level 1 we should always create a h3
			$html .= '<h3 class="cf options-'.$level1Key.'-icon">'.$level1Key.'<i class="fa fa-plus"></i><i class="fa fa-minus"></i><a name="accordion-level-'.$accordionLevel.'"></a></h3>';
			$summary .= '<div class="' . $optional . '"><h3>'.$optionalTitle.'</h3>';
			$html .= "<div id='accordion-level-{$accordionLevel}'><!-- nested content of {$level1Key}-->";
			// If this level contains the option
			if (array_key_exists('option', $level1Value))
			{
				$html .= $this->getOptionHtml($level1Value['option'], $level1Value['class']);
				$gotoNext = $accordionLevel + 1;
				$html .= '<a href="#" class="product-chooser-next" onclick="gotoAccordionTab('.$gotoNext.',0); return false;">Next <i class="fa fa-caret-right"></i></a>';
				$summary .= $this->getOptionSummaryHtml($level1Key, $level1Value);
			}
			else
			{
				if (!count($level1Value)) continue;
				$html .= "<div id='tabsNested' style='padding-bottom: 8px;' class='count-".count($level1Value)."'>";
				$html .= '<ul>';;
				$i = -1;
				foreach($level1Value as $level2Key => $level2Value)
				{
					$i++;
					$html .= '<li>';
					$html .= "<a href='#tabNested-{$i}'>{$level2Key}<span>&nbsp;/&nbsp;</span></a>";
					$html .= '</li>';
				}
				$html .= '</ul>';	
				$i = -1;
				foreach($level1Value as $level2Key => $level2Value)
				{
					$i++;
					$html .= "<div id='tabNested-{$i}' class='" . $level2Value['class'] . "'>";
                    $html .= "<div class='option-title2'>" . $level2Value['title2'] . "</div>";
					if ($i == (count($level1Value)-1))
					{
						$gotoAccordionLevel = $accordionLevel + 1;
						$gotoTabLevel = 0;
					}
					else
					{
						$gotoAccordionLevel = $accordionLevel;
						$gotoTabLevel = $i + 1;
					}
					if (array_key_exists('option', $level2Value))
					{
                        if ($optionalTitle == "Size") {
                            $html .= "<div class='option-title2'>(<a class='option-sub' target='_blank' href='/size-chart/'>view size chart</a>)</div>";
                        }

						$html .= $this->getOptionHtml($level2Value['option'], $level2Value['class']);

						if ($optionalTitle == "Size") {
							$html .= '<a href="#" class="product-chooser-next" style="float: left;"';
							$html .= ' onclick="gotoAccordionTab('.$gotoAccordionLevel.','.$gotoTabLevel.'); return false;">Next <i class="fa fa-caret-right"></i></a>';
							$html .= '<a href="/plus-size.html" class="product-chooser-next" style="width: 200px; float: right; background-color: black; text-align: center; margin-top: -32px; margin-bottom: -30px;">';
							$html .= '<strong>NEW!</strong><br> Click here to see our large range of XXS, 3XL and 4XL styles</a>';
						}
						else {
							$html .= '<a href="#" class="product-chooser-next" style="float: left;"';
							$html .= ' onclick="gotoAccordionTab('.$gotoAccordionLevel.','.$gotoTabLevel.'); return false;">Next <i class="fa fa-caret-right"></i></a>';
						}
						
						$html .= $this->_getViewMoreInformationLink($level1Key, $level2Key);

						$summary .= $this->getOptionSummaryHtml($level2Key, $level2Value, $accordionLevel, $i);
					}
					else
					{
                        foreach($level2Value as $level3Key => $level3Value) {
                            if (!$titleAdded && $level3Value['title2'] != '') {
                                $html .= "<div class='option-title2'>" . $level3Value['title2'] . "</div>";
                                break;
                            }
                        }
						foreach($level2Value as $level3Key => $level3Value)
						{
							$html .= '<div class="level3-option">';
							$html .= $this->getOptionHtml($level3Value['option'], $level3Value['class']);
							$html .= '</div>';
							$summary .= $this->getOptionSummaryHtml($level3Key, $level3Value, $accordionLevel, $i);
						} 
						$html .= '<a href="#_" class="product-chooser-next" onclick="gotoAccordionTab('.$gotoAccordionLevel.','.$gotoTabLevel.');">Next <i class="fa fa-caret-right"></i></a>';
					}
					$html .= '</div>';
				}
				$html .= '</div>';
			}
			$html .= "<!-- end nested content of {$level1Key}--></div>";
			$summary .= "</div>";
		}
		$html .= '</div>';

        $html .= '<div id="product-summary" class="product-summary-area">'.$summary.'</div>' . PHP_EOL;
        $html .= '<script type="text/javascript">' . PHP_EOL;
        $html .= '$$("#accordion1 input, #accordion1 select").each(function(elm) { elm.toggleClassName("ignore-visibility"); });'.PHP_EOL;
        $html .= 'Validation.isVisibleOrig = Validation.isVisible;' . PHP_EOL;
        $html .= 'Validation.isVisible = function(elm) {'.PHP_EOL;
        $html .= 'if (elm.hasClassName("ignore-visibility")) { return true; } else { return Validation.isVisibleOrig(elm); }'.PHP_EOL;
        $html .= '};'.PHP_EOL;
        $html .= 'function gotoAccordionTab(accordionIndex, tabIndex) {'.PHP_EOL;
        $html .= ' jQuery("#accordion1").accordion("option","active",accordionIndex);'.PHP_EOL;
        $html .= ' jQuery("#accordion-level-"+accordionIndex+" #tabsNested").tabs("option","active",tabIndex);'.PHP_EOL;
        $html .= '}'.PHP_EOL;
        $html .= '</script>' . PHP_EOL;

        return $html;
    }

    protected function _getViewMoreInformationLink($label, $secondaryLabel)
    {
        $html = '';
        if (in_array($label, array('Colour', 'Size'))) {
            if ($label == "Colour") {
                $html .= '<a href="#'.strtolower(str_replace(' ', '-', $label)).'" class="fancybox show-additional-info">';
                $html .= 'View full colour chart';
                $html .= '</a>';
            } elseif ($label == "Size" && $secondaryLabel == "Size") {
                $html .= '<a href="#'.strtolower(str_replace(' ', '-', $label)).'" class="fancybox show-additional-info">';
                $html .= 'Size Chart';
                $html .= '</a>';

                $html .= '<a target="_blank" href="'.$this->_getUrl('measurement/index/index/').'" class="show-additional-info made-to-measure-form-link">';
                $html .= 'Made to Measure Form';
                $html .= '</a>';
            }
        }
        return $html;
    }

    public function getOptionSummaryHtml($name, $value, $accordionIndex, $tabIndex)
    {
	$option = $value['option'];
        if ($option instanceof Mage_Catalog_Model_Product_Type_Configurable_Attribute)
        {
            $optionId = $option->getAttributeId();
            $elementName = "super_attribute[{$optionId}]";
        }
        else
        {
            $optionId = $option->getOptionId();
            $elementName = "options[{$optionId}]";
        }
        if ($option->getData('is_require'))
        {
            $required = "required";
        }
        else
        {
            $required = "not-required";
        }
        $html = "<div id='option-summary-{$optionId}' class='option-summary-item not-selected {$required}'>";
	if ($value['content2'] != "") $name = $value["content2"];
        $html .= $name.": <span id='option-summary-{$optionId}-value'>";
        switch($option->getType())
        {
            case 'drop_down':
                $html .= '';
                break;
            case 'radio':
                $html .= '';
                break;
            default:
                if (!($option instanceof Mage_Catalog_Model_Product_Type_Configurable_Attribute)) {
                    $elementName.='[]';
                }
                $html.='';
                break;
        }
        $html .= "</span>";

        $html .= "<div class='summary-edit'><a href='#custom-options' onclick='gotoAccordionTab({$accordionIndex},{$tabIndex});'> Edit</a></div>";
        $html .= "<div class='summary-choose'><a href='#custom-options' onclick='gotoAccordionTab({$accordionIndex},{$tabIndex});'> Choose</a></div>";

        $html .= "<span id='option-summary-{$optionId}-advice'></span></div>";
        $html .= <<<EOF
					<script type='text/javascript'>
					elms = document.getElementsByName('{$elementName}');
				for(i=0;i<elms.length;i++)
				{
					elm = elms[i];
					elm.advaiceContainer = 'option-summary-{$optionId}-advice';
					elm.observe('change', function(ev) {
							e = ev.element();
							switch(e.type)
							{
							case 'select':
							case 'select-one':
							$('option-summary-{$optionId}-value').textContent = "";
							$('option-summary-{$optionId}-value').textContent = $('option-summary-{$optionId}-value').textContent + e.options[e.selectedIndex].text + ". ";    
							$('option-summary-{$optionId}').removeClassName('not-selected');
							$('option-summary-{$optionId}').addClassName('selected');
							addRestrictions(e, e.options[e.selectedIndex].text, 'select');
							break;
							case 'radio':
							$('option-summary-{$optionId}-value').innerHTML = "";
							$('option-summary-{$optionId}-value').innerHTML = $(e).next('span.label').down().innerHTML + " ";
							$('option-summary-{$optionId}').removeClassName('not-selected');
							$('option-summary-{$optionId}').addClassName('selected');
							addRestrictions(e, $(e).next('span.label').down().innerHTML, 'radio');
							break;
							case 'checkbox':
							$('option-summary-{$optionId}-value').innerHTML = "";
							if (e.checked)
							{
								$('option-summary-{$optionId}-value').innerHTML = $(e).next('span.label').down().down().innerHTML + " ";
							}
							else
							{
								$('option-summary-{$optionId}-value').innerHTML = 'No';
							}
							$('option-summary-{$optionId}').removeClassName('not-selected');
							$('option-summary-{$optionId}').addClassName('selected');
							default:
							break;
							}
							});
				}
				</script>
EOF;

        return $html;
    }

    public function getOptionHtml($option, $optionClass = '')
    {
        if ($option === null) return '';
        if (get_class($option) == 'Mage_Catalog_Model_Product_Type_Configurable_Attribute')
        {
            $this->_optionsBlock->setOption($option);
        }
        Mage::register('productpagestep_class', $optionClass);
        $html = $this->_optionsBlock->getOptionHtml($option);
        Mage::unregister('productpagestep_class');

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
