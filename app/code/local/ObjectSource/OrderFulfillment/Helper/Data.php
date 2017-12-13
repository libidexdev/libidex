<?php
class ObjectSource_OrderFulfillment_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getApprParams()
	{
		
		return array(
                    'header' => 'Appr',
                    'index' => 'type',
                    'filter_index' => 'approved',
                    'type' => 'text',
                    'width' => '10px',
                    'align' => 'left',
                    'renderer' => 'ObjectSource_OrderFulfillment_Block_Adminhtml_Renderer_Approved',
		    'filter_condition_callback' => array('ObjectSource_OrderFulfillment_Model_Observer', 'filterAppr')
		);

	}

	public function getLineItemsParams()
	{
		
		return array(
                    'header' => 'Line Items',
                    'index' => 'type',
                    'filter' => false,
		    'sortable' => false,
                    'type' => 'text',
                    'width' => '190px',
                    'renderer' => 'ObjectSource_OrderFulfillment_Block_Adminhtml_Renderer_Lineitems',
		);

	}

    public function getExpectedDispatch()
    {
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
        $delay = Mage::getModel('orderfulfillment/productiondates')->getProductionDelay();
        $finishTimestamp = Mage::getModel('core/date')->timestamp(strtotime("+$delay days"));
        return date('Y-m-d', $finishTimestamp);
    }
}
