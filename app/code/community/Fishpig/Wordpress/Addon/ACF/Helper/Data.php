<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Helper_Data extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Cache of ACF field models
	 *
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * Determine whether the extension is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('wordpress/extend/acf');
	}

	/**
	 * Retrieve an ACF value using a custom ACF options page
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getOptionValue($key)
	{
		return $this->getValue($key, 'options');
	}
	
	/**
	 * Retrieve an ACF value for a category
	 *
	 * @param string $key
	 * @param Fishpig_Wordpress_Model_Post_Category $category
	 * @return mixed
	 */
	public function getCategoryValue($key, Fishpig_Wordpress_Model_Post_Category $category)
	{
		return $this->getValue($key, 'category_' . $category->getId());
	}
	
	/**
	 * Retrieve an ACF value using a custom ACF options page
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getValue($key, $scope)
	{
		if (($field = $this->_getField($key, $scope)) !== false) {
			return $field->setScope($scope)->setValue($this->getWpOption($scope . '_' . $key))->render();
		}
		
		return false;
	}
	
	/**
	 * Retrieve a ACF value
	 * This is called via an observer
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function getAcfValueObserver(Varien_Event_Observer $observer)
	{
		$post = $observer->getEvent()->getObject();
		
		if (!$this->isEnabled() || !$post) {
			return false;
		}

		$meta = $observer->getEvent()->getMeta();

		if (($field = $this->_getField($meta->getKey())) !== false) {
			$meta->setValue(
				$field->setPost($post)->setValue($meta->getValue())->render()
			);
		}
		
		return $this;
	}
		
	/**
	 * Retrieve a field by it's meta_key and cache it
	 *
	 * @param string $key
	 * @return false|Fishpig_Wordpress_Addon_ACF_Model_Field
	 */
	protected function _getField($key, $scope = null)
	{

		if (!isset($this->_fields[$key])) {
			$this->_fields[$key] = false;

			$field = Mage::getModel('wp_addon_acf/field')->setScope($scope)->load($key);

			if ($field->getId()) {
				$this->_fields[$key] = $field;
			}
		}
		
		return $this->_fields[$key];			
	}
}
