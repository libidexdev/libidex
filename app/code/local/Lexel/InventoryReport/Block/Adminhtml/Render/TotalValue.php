<?php
class Lexel_InventoryReport_Block_Adminhtml_Render_TotalValue extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $price  = $row->getPrice();
        $qty = $row->getQty();
        $total = $row->getTotalRemaining();
        if ($price == null) {
            return '£'.number_format($total, 2);
        }
        return '£'.number_format($price * $qty, 2);
    }
}