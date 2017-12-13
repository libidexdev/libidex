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

class Magegiant_GiftCard_Block_Adminhtml_Catalog_Product_Renderer_Amount
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Label of delete button
     *
     * @var unknown_type
     */
    protected $_deleteButtonLabel;

    protected function _prepareToRender()
    {
        $this->addColumn('amount', array(
            'label'    => Mage::helper('giftcard')->__('Amount'),
            'style'    => "width: 74px !important",
            'class'    => "input-text required-entry validate-zero-or-greater",
            'renderer' => Mage::getBlockSingleton('giftcard/adminhtml_catalog_product_renderer_amount_price')
        ));

        $this->addColumn('price', array(
            'label'    => Mage::helper('giftcard')->__('Price'),
            'style'    => "width: 74px !important",
            'class'    => "input-text required-entry validate-zero-or-greater",
            'renderer' => Mage::getBlockSingleton('giftcard/adminhtml_catalog_product_renderer_amount_price')
        ));

        $this->_addAfter          = false;
        $this->_addButtonLabel    = Mage::helper('giftcard')->__('Add Amount');
        $this->_deleteButtonLabel = '';

        $this->setTemplate('magegiant/giftcard/catalog/product/renderer/array.phtml');

        Mage::dispatchEvent('magegiant_giftcard_product_prepare_column', array(
            'grid' => $this
        ));

        return $this;
    }

    public function setVariableData($name, $value)
    {
        $this->$name = $value;

        return $this;
    }
}
