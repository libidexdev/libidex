<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Flexible_Content extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Process the flexible content field
	 *
	 * @return $this
	 */
	protected function _render()
	{
		$layouts = $this->getField()->getLayouts();
		$data = array();
		
		$blueprints = @unserialize($this->getField()->getValue());

		foreach($layouts as $it => $layout) {
			$data[!empty($layout['name']) ? $layout['name'] : $it] = $layout['sub_fields'];
		}

		$values = array();
		
		foreach($blueprints as $it => $layout) {
			$subFieldValues = array();

			foreach($data[$layout] as $subFieldId => $subFieldData) {
				$subFieldValues[$subFieldData['name']] = $this->_renderSubField($subFieldData, $this->getField()->getName() . '_' . $it . '_');
			}
			
			$values[] = array_merge(array('layout' => $layout), $subFieldValues);
		}

		$this->setValue($values);
		
		return parent::_render();
	}
}
