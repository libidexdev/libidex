<?php
class Lexel_Dropship_Model_Observer
{
	public function preDispatchShippingMethodSave($observer)
	{
		/**
		 * @var $controller Mage_Checkout_OnepageController
		 */
		$controller = $observer->getControllerAction();

		if ($controller->getRequest()->isPost()) {
			$data = $controller->getRequest()->getPost('shipping_method', '');
			if (!empty($data)) {
				$controller->getOnepage()->saveSingleWarehouseShippingMethod($data);
				return;
			}

			$data = $controller->getRequest()->getPost();

			if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon','shipping/wsafreightcommon/active')){
				$attributeCodes = Mage::helper('wsafreightcommon')->getAllAccessoryCodes();

				foreach ($attributeCodes as $code) {
					unset($data[$code]);
				}

			}
			foreach ( array('location_id','pickup_date','pickup_slot','pickup_zipcode','dropship_date_input','dropship_extrainfo_input') as $storePickupExclId) {
				if (array_key_exists($storePickupExclId,$data)) {
					unset($data[$storePickupExclId]);  // remove store pickup parameters
				}
			}

			$result = $controller->getOnepage()->saveWarehouseShippingMethod($data);

			/*
			$result will have error data if shipping method is empty
			*/
			if(!$result) {
				Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$controller->getRequest(), 'quote'=>$controller->getOnepage()->getQuote()));
				$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

				$result['goto_section'] = 'payment';
				$result['update_section'] = array(
					'name' => 'payment-method',
					'html' => $this->_getPaymentMethodsHtml($controller)
				);

				if (Mage::helper('core')->isModuleEnabled('EcomDev_CheckItOut')) {
					$reflection = new ReflectionObject($controller);
					if ($reflection->hasMethod('_addHashInfo')) {
						$method = $reflection->getMethod('_addHashInfo');
						$method->setAccessible(true);
						$method->invokeArgs($controller, array(&$result));
						$method->setAccessible(false);
					}
				}
			}
		}
	}
}