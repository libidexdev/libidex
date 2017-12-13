<?php
class ObjectSource_RapidOrder_Block_Adminhtml_Renderer_Rapidselection extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Only render coupon codes which match our RAPID prefix.
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row)
    {
        $couponCode = $row->getCouponCode();
        if (strpos($couponCode, 'RAPID') !== false) {
            return $couponCode;
        }

        // check for 'Delivery' attribute set product in order
        $orderId = $row->getEntityId();
        $isNewRapid = Mage::helper('os_rapidservice')->getIsRapidServiceItemByOrderId($orderId);

        return ($isNewRapid) ? 'RAPIDSILVER' : '';
    }
}
