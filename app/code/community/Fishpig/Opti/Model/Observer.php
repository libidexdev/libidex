<?php
/**
 * @category	Fishpig
 * @package		Fishpig_Opti
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 * @info			http://fishpig.co.uk/magento-optimisation.html
 */

class Fishpig_Opti_Model_Observer extends Varien_Object
{
	/**
	 * Minify the HTML currently in the HTML Body
	 *
	 * @param Varien_Event_Observer $observer)
	 * @return $this
	 */
	public function minifyHtmlObserver(Varien_Event_Observer $observer)
	{
		if (!$this->isAllowedRoute()) {
			return $this;
		}

		$html = $observer
			->getEvent()
				->getFront()
					->getResponse()
						->getBody();

		if (Mage::getStoreConfigFlag('opti/html/minify')) {
			$html = Mage::helper('opti/minify_html')->minify($html);
		}
		
		$html = Mage::helper('opti/minify_html')->clean($html);

		$observer->getEvent()
			->getFront()
				->getResponse()
					->setBody($html);

		return $this;
	}

	/**
	 * Minify all items
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function minifyJsCssObserver(Varien_Event_Observer $observer)
	{
		if (!($head = Mage::getSingleton('core/layout')->getBlock('head'))) {
			return $this;
		}
		
		if (!($items = $head->getItems())) {
			return $this;
		}
		
		if (!$this->isAllowedRoute()) {
			return $this;
		}

		if (Mage::getStoreConfigFlag('opti/css/minify')) {
			$items = Mage::helper('opti/minify_css')->minifyHeadItems($items);
		}
		
		if (Mage::getStoreConfigFlag('opti/js/minify')) {
			$items = Mage::helper('opti/minify_js')->minifyHeadItems($items);
		}
		
		/*
		if (Mage::getStoreConfigFlag('opti/js/merge') && !Mage::getStoreConfigFlag('dev/js/merge_files')) {
			$items = Mage::helper('opti/minify_js')->mergeHeadItems($items);
		}
		*/
		
		$head->setItems($items);

		return $this;
	}
	
	/**
	 * Determine whether to run on the route
	 *
	 * @return bool
	 */
	public function isAllowedRoute()
	{
		foreach((array)Mage::app()->getResponse()->getHeaders() as $header) {
			if (isset($header['name']) && strtolower($header['name']) === 'content-type') {
				$isTextHtml = strpos($header['value'], 'text/html') !== false;
			}
		}

		if (!$isTextHtml) {
			return false;
		}

		$allowedModules = (array)explode(',', trim(Mage::getStoreConfig('opti/conditions/modules'), ','));

		return in_array(Mage::app()->getRequest()->getModuleName(), $allowedModules);
	}
}
