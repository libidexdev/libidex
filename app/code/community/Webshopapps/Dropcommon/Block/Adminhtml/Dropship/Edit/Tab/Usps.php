<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tab_Usps extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldsetUSPS = $form->addFieldset('dropship_form', array('legend' => Mage::helper('dropcommon')->__('USPS Login Details  (leave empty to use defaults)')));

        Mage::helper('dropcommon')->addDimensionalWarning($fieldsetUSPS);

		$fieldsetUSPS->addField('usps_user_id', 'text', array(
				'label' => Mage::helper('dropcommon')->__('User ID'),
				'required' => false,
				'name' => 'usps_user_id',
		));

		$fieldsetUSPS->addField('usps_password', 'password', array(
				'label' => Mage::helper('dropcommon')->__('Password'),
				'required' => false,
				'name' => 'usps_password',
		));

		$allowedMethods = array();
		$options = array();

		$carrierConfig = Mage::getModel('shipping/config');

		if (empty($allowedMethods)) {

			$allowedMethods = $carrierConfig->getCarrierInstance('usps')->getAllowedMethods();
			$options = $this->getAllowedMethods($allowedMethods);
			array_unshift($options, array('value' => '', 'label' => Mage::helper('shipping')->__('USE DEFAULT')));
		}

		$fieldsetUSPS->addField('usps_allowed_methods', 'multiselect', array(
				'label' => Mage::helper('dropcommon')->__('Allowed Methods'),
				'required' => false,
				'name' => 'usps_allowed_methods',
				'values' => $options,
		));

		if (Mage::getSingleton('adminhtml/session')->getDropshipData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipData());
			Mage::getSingleton('adminhtml/session')->setDropshipData(null);
		} elseif (Mage::registry('dropship_data')) {
			$form->setValues(Mage::registry('dropship_data')->getData());

			$form->getElement('usps_allowed_methods')->setValue(Mage::registry('dropship_data')->getUspsAllowedMethods());
		}


		return parent::_prepareForm();
	}

	// get the allowed methods and put in a suitable array
	private function getAllowedMethods($allowedMethods) {

		$options = array();

		if (!is_array($allowedMethods)) {
			return $options;
		}

		foreach ($allowedMethods as $value => $label) {
            if($label == '') {
                $label = $value;
            }
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

		return $options;
	}

}