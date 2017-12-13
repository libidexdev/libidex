<?php
class ObjectSource_OrderFulfillment_Model_Observer
{
    public function salesOrderGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderGridCollection();
        $select = $collection->getSelect();
        //$select->joinLeft(array('d'=>$collection->getTable('dropcommon/dropship')), 'payment.parent_id=main_table.entity_id',array('payment_method'=>'method'));
    }

    public function salesQuoteItemSetFulfillmentData($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $parentItemId = $quoteItem->getParentItemId();
        if (empty($parentItemId))
        {
            $product = $observer->getProduct();
            $supplierId = $product->getSupplier();
            if (!empty($supplierId))
            {
                $fulfillmentData = serialize(array('supplier' => $product->getSupplier()));
                $quoteItem->setFulfillmentData($fulfillmentData);
                //Mage::log('fulfillment_data: '.print_r($fulfillmentData,true), null, 'OS_OrderFulfillment.log');
            }
        }
    }

    public function addJobTicketPrintMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_order')
        {

            $block->addItem('orderfulfillment', array(
                'label' => 'Approve',
                'url' => Mage::helper("adminhtml")->getUrl('orderfulfillment/adminhtml_index/approve'),
            ));

            $block->addItem('orderfulfillment1', array(
                'label' => 'Print for Production',
                'url' => Mage::helper("adminhtml")->getUrl('orderfulfillment/adminhtml_index/jobTicketLoader'),
            ));
        }
    }

    public function setInitOrderFulfillmentData($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());

        $delay = Mage::getModel('orderfulfillment/productiondates')->getProductionDelay();

        $finishTimestamp = Mage::getModel('core/date')->timestamp(strtotime("+$delay days"));
        $fulfillmentData = serialize(array('start_date' => date('Y-m-d', $currentTimestamp),
            'finish_date' => date('Y-m-d', $finishTimestamp)
        ));
        $order->setFulfillmentData($fulfillmentData);
    }

    public function generateSellingReport($observer)
    {
        $popularThreshold = Mage::getStoreConfig('orderfulfillment/orderfulfillment_group/popular_threshold');
        $popularFrom = Mage::getStoreConfig('orderfulfillment/orderfulfillment_group/popular_from');

        //$order = $observer->getEvent()->getOrder();
        $from = date('Y-m-d', strtotime("-$popularFrom days"));
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from'  => $from));
        $table = array();
        foreach ($orders as $order) {
            $items = Mage::getModel("sales/order_item")->getCollection()
                ->addFieldToFilter("parent_item_id", array('null' => true))
                ->addFieldToFilter("order_id", $order->getEntityId());

            foreach ($items as $item) {
                if (empty($table[$item->getSku()]))
                    $table[$item->getSku()] = $item->getQtyOrdered();
                else
                    $table[$item->getSku()] = $table[$item->getSku()] + $item->getQtyOrdered();
            }
        }

        foreach ($table as $sku => $qty) {
            if ($qty >= $popularThreshold) {
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

                if ($product) {
                    $lastPopularAt = $product->getLastPopularAt();
                    if ( empty($lastPopularAt) || (strtotime($lastPopularAt) < strtotime("-$popularThreshold days")) ) {
                        echo 'Set as popular and send email';

                        $emailTemplate  = Mage::getModel('core/email_template')
                            ->loadDefault('selling_report_email_template');

                        $emailTemplateVariables = array();
                        $emailTemplateVariables['product_name'] = $product->getName();
                        $emailTemplateVariables['product_sku'] = $sku;
                        $emailTemplateVariables['sold'] = $qty;
                        $emailTemplateVariables['days'] = $popularFrom;

                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $emailTemplate->send('sales@libidex.co.uk','Sales', $emailTemplateVariables);

                        //echo $processedTemplate;

                        $storeId = Mage::app()->getStore()->getStoreId();
                        // We are not allowed to set product data from frontend
                        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                        $lastPopularAt = date('Y-m-d H:m:s', time());
                        $product->setLastPopularAt($lastPopularAt);
                        $product->save();
                        Mage::app()->setCurrentStore($storeId);
                    }
                }
            }
        }
    }

    public function updateGridWithFulfillmentData(Varien_Event_Observer $observer)
    {
	$london = '18';
	$malaysia = '17';
	
	$resource = Mage::getSingleton('core/resource');
	$writeConnection = $resource->getConnection('core_write');

	$query = "update sales_flat_order_grid a inner join (select entity_id as order_id, CASE WHEN fulfillment_data LIKE '%approved%' THEN 'Y' ELSE 'N' END as approved from sales_flat_order) b on a.entity_id = b.order_id set appr = approved where appr <> 'Y';";

	$writeConnection->query($query);

	$query = "update sales_flat_order_grid a inner join (select b.entity_id as order_id, a.supplier from sales_flat_order_grid b inner join (select order_id, CASE WHEN LENGTH(supplier) > 1 THEN 'H' ELSE supplier END supplier from (select order_id, GROUP_CONCAT(DISTINCT CASE WHEN fulfillment_data like '%\"supplier\";s:2:\"${london}\"%' THEN 'L' WHEN fulfillment_data like '%\"supplier\";s:2:\"${malaysia}\"%'THEN 'M' ELSE 'E' END SEPARATOR '')supplier from sales_flat_order_item where parent_item_id is null group by order_id) AS supplier) a on b.entity_id = a.order_id) b on a.entity_id = b.order_id set code = supplier where code = '' or code is null;";
	
	$writeConnection->query($query);
    }
}
