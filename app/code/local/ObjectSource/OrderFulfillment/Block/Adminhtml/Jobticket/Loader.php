<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Jobticket_Loader extends ObjectSource_OrderFulfillment_Block_Adminhtml_Jobticket_Abstract
{
    public function getProcessUrl()
    {
        return Mage::helper("adminhtml")->getUrl('orderfulfillment/adminhtml_index/jobTicketPreview',
            array('order_ids' => $this->getOrderIds()));
    }

    public function getRedirectUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/index');
    }

    protected function _afterToHtml($html2)
    {
        $html1 = '<script type="text/javascript">';
        $html1 .= 'var orderIds = '.json_encode($this->getOrderIds()).';';
        $html1 .= '</script>';
        return $html1 . $html2;
    }
}