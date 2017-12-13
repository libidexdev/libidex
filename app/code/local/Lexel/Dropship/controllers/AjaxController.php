<?php
require_once Mage::getModuleDir('controllers', 'Idev_OneStepCheckout') . DS . 'AjaxController.php';
class Lexel_Dropship_AjaxController extends Idev_OneStepCheckout_AjaxController
{
	public function add_couponAction()
	{

		$this->_checkSession();

		// osc checkout helper
		$oscch = Mage::helper('onestepcheckout/checkout');

		$quote = $this->_getOnepage()->getQuote();
		$couponCode = (string)$this->getRequest()->getParam('code');

		if ($this->getRequest()->getParam('remove') == 1) {
			$couponCode = '';
		}

		$response = array(
			'success' => false,
			'error'=> false,
			'message' => false,
		);



		try {
			$quote->getShippingAddress()->setCollectShippingRates(true);

			// ajaxcontroller add_couponAction 1
			if ($oscch->isYesSwitch_CollectTotal_AjaxcontrollerPhp_AddCouponAction1()) {
				$quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
					->collectTotals()
					->save();
			}

			if ($couponCode) {
				if ($couponCode == $quote->getCouponCode()) {
					$response['success'] = true;
					$response['message'] = $this->__('Coupon code "%s" was applied successfully.', Mage::helper('core')->escapeHtml($couponCode));
				}
				else {
					$response['success'] = false;
					$response['error'] = true;
					$response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
				}
			} else {
				$response['success'] = true;
				$response['message'] = $this->__('Coupon code was canceled successfully.');
			}
		}
		catch (Mage_Core_Exception $e) {
			$response['success'] = false;
			$response['error'] = true;
			$response['message'] = $e->getMessage();
		}
		catch (Exception $e) {
			$response['success'] = false;
			$response['error'] = true;
			$response['message'] = $this->__('Can not apply coupon code.');
		}




		$html = $this->getLayout()
			->createBlock('dropship/checkout_onepage_shipping_method_available')
			->setTemplate('lexel/dropship/checkout/onestepcheckout/shipping_method/available.phtml')
			->toHtml();

		$response['shipping_method'] = $html;


		$html = $this->getLayout()
			->createBlock('checkout/onepage_payment_methods', 'choose-payment-method')
			->setTemplate('onestepcheckout/payment_method.phtml');

		if(Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()){
			if (Mage::helper('onestepcheckout')->hasEeCustomerbalanace()) {
				$customerBalanceBlock = $this->getLayout()->createBlock(
					'enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array(
						'template' => 'onestepcheckout/customerbalance/payment/additional.phtml'
					)
				);
				$customerBalanceBlockScripts = $this->getLayout()->createBlock(
					'enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array(
						'template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'
					)
				);
				$this->getLayout()
					->getBlock('choose-payment-method')
					->append($customerBalanceBlock)
					->append($customerBalanceBlockScripts);
			}

			if (Mage::helper('onestepcheckout')->hasEeRewards()) {
				$rewardPointsBlock = $this->getLayout()->createBlock(
					'enterprise_reward/checkout_payment_additional', 'reward.points', array(
						'template' => 'onestepcheckout/reward/payment/additional.phtml',
						'before' => '-'
					)
				);
				$rewardPointsBlockScripts = $this->getLayout()->createBlock(
					'enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
						'template' => 'onestepcheckout/reward/payment/scripts.phtml',
						'after' => '-'
					)
				);
				$this->getLayout()
					->getBlock('choose-payment-method')
					->append($rewardPointsBlock)
					->append($rewardPointsBlockScripts);
			}
		}

		if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('onestepcheckout')->hasEeGiftcards()) {
			$giftcardScripts = $this->getLayout()->createBlock(
				'enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array(
					'template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'
				)
			);
			$html->append($giftcardScripts);
		}

		$response['payment_method'] = $html->toHtml();

		// Add updated totals HTML to the output
		$html = $this->getLayout()
			->createBlock('onestepcheckout/summary')
			->setTemplate('onestepcheckout/summary.phtml')
			->toHtml();

		$response['summary'] = $html;

		$this->getResponse()->setBody(Zend_Json::encode($response));
	}

	public function add_giftcardAction()
	{
		$this->_checkSession();
		$response = array(
			'success' => false,
			'error'=> true,
			'message' => $this->__('Cannot apply Gift Card, please try again later.'),
		);

		$code = $this->getRequest()->getParam('code', false);
		$remove = $this->getRequest()->getParam('remove', false);

		if (!empty($code) && empty($remove)) {
			try {
				Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
					->loadByCode($code)
					->addToCart();

				$response['success'] = true;
				$response['error'] = false;
				$response['message'] = $this->__('Gift Card "%s" was added successfully.', Mage::helper('core')->escapeHtml($code));
			} catch (Mage_Core_Exception $e) {
				Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));

				$response['success'] = false;
				$response['error'] = true;
				$response['message'] = $e->getMessage();
			} catch (Exception $e) {
				Mage::getSingleton('checkout/session')->addException(
					$e,
					$this->__('Cannot apply Gift Card, please try again later.')
				);

				$response['success'] = false;
				$response['error'] = true;
				$response['message'] = $this->__('Cannot apply Gift Card, please try again later.');
			}
		}

		if(!empty($remove)){
			$codes = $this->_getOnepage()->getQuote()->getGiftCards();
			if(!empty($codes)){
				$codes = unserialize($codes);
			} else {
				$codes = array();
			}

			$response['message'] = $this->__('Cannot remove Gift Card, please try again later.');
			$messageCodes = array();
			foreach($codes as $value){
				try {
					Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
						->loadByCode($value['c'])
						->removeFromCart();
					$messageCodes[] = $value['c'];
					$response['success'] = true;
					$response['error'] = false;
					$response['message'] = $this->__('Gift Card "%s" was removed successfully.', Mage::helper('core')->escapeHtml(implode(', ', $messageCodes)));
				} catch (Mage_Core_Exception $e) {
					$response['success'] = false;
					$response['error'] = true;
					$response['message'] = $e->getMessage();
				} catch (Exception $e) {
					Mage::getSingleton('checkout/session')->addException(
						$e,
						$this->__('Cannot remove Gift Card, please try again later.')
					);

					$response['success'] = false;
					$response['error'] = true;
					$response['message'] = $this->__('Cannot remove Gift Card, please try again later.');
				}
			}
		}



		// Add updated totals HTML to the output
		$html = $this->getLayout()
			->createBlock('onestepcheckout/summary')
			->setTemplate('onestepcheckout/summary.phtml')
			->toHtml();

		$response['summary'] = $html;

		$html = $this->getLayout()
			->createBlock('dropship/checkout_onepage_shipping_method_available')
			->setTemplate('lexel/dropship/checkout/onestepcheckout/shipping_method/available.phtml')
			->toHtml();

		$response['shipping_method'] = $html;

		$html = $this->getLayout()
			->createBlock('checkout/onepage_payment_methods')
			->setTemplate('onestepcheckout/payment_method.phtml');

		if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('onestepcheckout')->hasEeGiftcards()) {
			$giftcardScripts = $this->getLayout()->createBlock(
				'enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array(
					'template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'
				)
			);
			$html->append($giftcardScripts);
		}

		$response['payment_method'] = $html->toHtml();

		$this->getResponse()->setBody(Zend_Json::encode($response));
	}

	public function updatecartAction()
	{

		$this->_checkSession();

		$response = array(
			'success' => false,
			'error'=> false,
			'message' => false
		);

		try {
			$cartData = $this->getRequest()->getParam('cart');

			if (!empty($cartData) && is_array($cartData)) {
				$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
				);
				foreach ($cartData as $index => $data) {
					if (isset($data['qty'])) {
						$cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
					}
				}

				$cart = Mage::getSingleton('checkout/cart');
				if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
					$cart->getQuote()->setCustomerId(null);
				}

				$cartData = $cart->suggestItemsQty($cartData);
				$cart->updateItems($cartData)
					->save();
				Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
			} else {
				Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot update shopping cart.'));
				$response = array(
					'success' => false,
					'error'=> true,
					'message' => 'No cart data here',
					'redirect' => Mage::getUrl('checkout/cart')
				);
			}
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('checkout/session')->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
			$response = array(
				'success' => false,
				'error'=> true,
				'message' => Mage::helper('core')->escapeHtml($e->getMessage()),
				'redirect' => Mage::getUrl('checkout/cart')
			);
		} catch (Exception $e) {
			Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot update shopping cart.'));
			$response = array(
				'success' => false,
				'error'=> true,
				'message' => $this->__('Cannot update shopping cart.'),
				'redirect' => Mage::getUrl('checkout/cart')
			);
			Mage::logException($e);
		}



		$response = array(
			'success' => true,
			'error'=> false,
			'message' => 'Items upated',
			'redirect' => ''
		);

		if(!$cart->getQuote()->hasItems()){
			$response['redirect'] = Mage::getUrl('checkout/cart');
		}

		$html = $this->getLayout()
			->createBlock('dropship/checkout_onepage_shipping_method_available')
			->setTemplate('lexel/dropship/checkout/onestepcheckout/shipping_method/available.phtml')
			->toHtml();

		$response['shipping_method'] = $html;


		$html = $this->getLayout()
			->createBlock('checkout/onepage_payment_methods', 'choose-payment-method')
			->setTemplate('onestepcheckout/payment_method.phtml');

		if(Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()){
			$customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template'=>'onestepcheckout/customerbalance/payment/additional.phtml'));
			$customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template'=>'onestepcheckout/customerbalance/payment/scripts.phtml'));

			$rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array('template'=>'onestepcheckout/reward/payment/additional.phtml', 'before' => '-'));
			$rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array('template'=>'onestepcheckout/reward/payment/scripts.phtml', 'after' => '-'));

			$this->getLayout()->getBlock('choose-payment-method')
				->append($customerBalanceBlock)
				->append($customerBalanceBlockScripts)
				->append($rewardPointsBlock)
				->append($rewardPointsBlockScripts);
		}

		if(Mage::helper('onestepcheckout')->isEnterprise()){
			$giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template'=>'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
			$html->append($giftcardScripts);
		}

		$response['payment_method'] = $html->toHtml();

		// Add updated totals HTML to the output
		$html = $this->getLayout()
			->createBlock('onestepcheckout/summary')
			->setTemplate('onestepcheckout/summary.phtml')
			->toHtml();

		$response['summary'] = $html;

		$this->getResponse()->setBody(Zend_Json::encode($response));
	}

	public function getOnepage()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}
}