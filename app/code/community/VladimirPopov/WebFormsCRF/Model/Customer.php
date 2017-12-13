<?php
class VladimirPopov_WebFormsCRF_Model_Customer
	extends Mage_Customer_Model_Customer
{
	public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
    	if(Mage::getStoreConfig('webformscrf/registration/disable_welcome_email')) return $this;
    	return parent::sendNewAccountEmail($type, $backUrl, $storeId);
    }
}