<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ZeroSellers
 */

class  Amasty_ZeroSellers_Block_Adminhtml_Purchased_Renderer_Discount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    function render(Varien_Object $row)
    {
        return round(parent::render($row), 2);
    }
}