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

class Magegiant_GiftCard_Block_Adminhtml_Catalog_Product_Renderer_Amount_Price
	extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
	{
		$inputName  = $this->getInputName();
		$columnName = $this->getColumnName();
		$column     = $this->getColumn();

		$html = '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
			($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
			(isset($column['class']) ? $column['class'] : 'input-text') . '"'.
			(isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';

		$html .= ' <strong>'.Mage::app()->getBaseCurrencyCode().'</strong>';

		return $html;
	}
}
