<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract extends Varien_Object
{
	/**
	 * Render and return the value
	 *
	 * @return mixed
	 */
	final public function render()
	{
		if (Mage::helper('wp_addon_acf')->isEnabled()) {
			$this->setOriginalValue($this->getValue());
			
			$this->_beforeRender();
			$this->_render();		
			$this->_afterRender();
	
			return $this->getValue();
		}
		
		return null;
	}

	/**
	 * Can be extended in child classes to make changes to value
	 * before self::_render()
	 *
	 * @return $this
	 */
	protected function _beforeRender()
	{
		if ($this->isSerialized()) {
			$this->setValue(unserialize($this->getValue()));
		}

		return $this;
	}
	
	/**
	 * Can be extended in child classes to render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
		return $this;
	}

	/**
	 * Can be extended in child classes to make changes to value
	 * after self::_render()
	 *
	 * @return $this
	 */
	protected function _afterRender()
	{
		if (is_string($this->getValue())) {
			if (!$this->getField()->getFormatting() === 'html') {
				$this->setValue(strip_tags($this->getValue()));
			}
		}
		
		return $this;
	}
	
	/**
	 * Determine whether the value is serialized
	 *
	 * @return bool
	 */
	public function isSerialized()
	{
		if ($value = $this->getValue()) {
			if (!in_array(gettype($value), array('object', 'array'))) {
				if (@unserialize($value) !== false) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Render a sub field
	 *
	 * @param array $fieldData
	 * @param string $prefix = ''
	 * @return array
	 */
	protected function _renderSubField($fieldData, $prefix = '')
	{
		$metaKey = $prefix . $fieldData['name'];

		$field = Mage::getModel('wp_addon_acf/field')
			->setData($fieldData)
			->setField($this);

		if ($field->getType() === 'repeater') {
			if (isset($fieldData['sub_fields']) && is_array($fieldData['sub_fields'])) {
				return $this->_renderRepeaterSubFields($fieldData['sub_fields'], $metaKey . '_');
			}
		}
		else {
			if ($this->getPost()) {
				$field->setPost($this->getPost())
					->setValue(
						$this->getPost()->getResource()->getMetaValue($this->getPost(), $metaKey)
					);
			}
			else if ($this->getScope()) {
				$field->setValue(
					Mage::helper('wordpress')->getWpOption($this->getScope() . '_' . $metaKey)
				);
			}

			return $field->render();
		}
	}
	
	/**
	 * Render the sub fields for the repeater
	 * This allows for repeater fields to be embedded inside this repeater field
	 *
	 * @param array $fields
	 * @param string $prefix = ''
	 * @return array
	 */
	protected function _renderRepeaterSubFields(array $fields, $prefix = '')
	{
		$values = array();
		
		if ($this->getScope() === 'options') {
			$max = (int)Mage::helper('wordpress')->getWpOption('options_' . $this->getField()->getName());
		}
		else {
			$max = $this->getPost()
				->getResource()
					->getMetaValue($this->getPost(), rtrim($prefix, '_'));
		}

		for($it = 0; $it < $max; $it++) {
			foreach($fields as $fieldId => $data) {
				$values[$it][$data['name']] = $this->_renderSubField($data, $prefix . $it . '_');
			}
		}

		return $values;
	}
}
