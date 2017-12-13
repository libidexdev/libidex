<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tab_Fedex extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldsetFedex = $form->addFieldset('dropship_form', array('legend' => Mage::helper('dropcommon')->__('Fedex Login Details  (leave empty to use defaults)')));
		$fieldsetFedexSoap = $form->addFieldset('dropship_soap', array('legend' => Mage::helper('dropcommon')->__('Fedex SOAP Additional Login Details')));
        Mage::helper('dropcommon')->addDimensionalWarning($fieldsetFedex);

		$fieldsetFedex->addField('fedex_account_id', 'text', array(
				'label' => Mage::helper('dropcommon')->__('Account ID'),
				'required' => false,
				'name' => 'fedex_account_id',
		));

		$fieldsetFedexSoap->addField('fedexsoap_key', 'password', array(
				'label' => Mage::helper('dropcommon')->__('Key'),
				'required' => false,
				'name' => 'fedexsoap_key',
		));

		$fieldsetFedexSoap->addField('fedexsoap_password', 'password', array(
				'label' => Mage::helper('dropcommon')->__('Password'),
				'required' => false,
				'name' => 'fedexsoap_password',
		));

		$fieldsetFedexSoap->addField('fedexsoap_meter_number', 'password', array(
				'label' => Mage::helper('dropcommon')->__('Meter Number'),
				'required' => false,
				'name' => 'fedexsoap_meter_number',
		));

        $fieldsetFedexSoap->addField('fedexsoap_hub_id', 'text', array(
            'label' => Mage::helper('dropcommon')->__('SmartPost Hub ID'),
            'required' => false,
            'name' => 'fedexsoap_hub_id',//DROP-96
        ));

		$allowedMethods = array();
		$options = array();

		if (empty($allowedMethods)) {

			$allowedMethods = Mage::getModel('usa/shipping_carrier_fedex')->getCode('method');
			$options = $this->getAllowedMethods($allowedMethods);
			array_unshift($options, array('value' => '', 'label' => Mage::helper('shipping')->__('USE DEFAULT')));
		}

		$fieldsetFedexSoap->addField('fedexsoap_allowed_methods', 'multiselect', array(
				'label' => Mage::helper('dropcommon')->__('Allowed Methods'),
				'required' => false,
				'name' => 'fedexsoap_allowed_methods',
				'values' => $options,
		));

		if (Mage::getSingleton('adminhtml/session')->getDropshipData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipData());
			Mage::getSingleton('adminhtml/session')->setDropshipData(null);
		} elseif (Mage::registry('dropship_data')) {
			$form->setValues(Mage::registry('dropship_data')->getData());

			$form->getElement('fedexsoap_allowed_methods')->setValue(Mage::registry('dropship_data')->getFedexsoapAllowedMethods());
		}



		return parent::_prepareForm();
	}

	// get the allowed methods and put in a suitable array
	private function getAllowedMethods($allowedMethods) {

		$options = array();
		if (!is_array($allowedMethods)) {
			return $options;
		}

		//todo store id
		foreach ($allowedMethods as $value => $label) {

			$options[] = array(
					'value' => $value,
					'label' => $label
			);
		}

		return $options;
	}

}