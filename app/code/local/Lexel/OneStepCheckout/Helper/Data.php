<?php
class Lexel_OneStepCheckout_Helper_Data extends Idev_OneStepCheckout_Helper_Data
{
	public function setCustomerComment($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$enableComments = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments');
		$enableCommentsDefault = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments_default');
		$orderComment = $this->_getRequest()->getPost('onestepcheckout_comments');
		$orderComment = trim($orderComment);

		/* Set customer comment when using third party payment methods */
		if (!$order->getOnestepcheckoutCustomercomment()) {
			if ($orderComment !== "") {
				$order->setOnestepcheckoutCustomercomment($orderComment);
			}
		}

		if($enableComments && $enableCommentsDefault) {
			if ($orderComment != ""){
				$order->setState($order->getStatus(), true, Mage::helper('core')->escapeHtml($orderComment), false);
			}
		}
	}
}