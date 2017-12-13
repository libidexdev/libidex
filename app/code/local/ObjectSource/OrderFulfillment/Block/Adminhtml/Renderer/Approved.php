<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Renderer_Approved extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private static $_supplierOptions = null;

    public function render(Varien_Object $row)
    {
        $order = Mage::getModel('sales/order')->load($row->getEntityId());
        $approved = $order->getFulfillmentDataValue('approved');
        if (!empty($approved))
            return 'Y';
        return 'N';
    }
}