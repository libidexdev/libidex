<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Image extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
		if ($value = $this->getValue()) {
			$image = Mage::getModel('wordpress/image')->load($this->getValue());
			
			if ($image->getId()) {
				if ($this->getField()->getSaveFormat() === 'object') {
					$this->setValue($image);
				}
				else if ($this->getField()->getSaveFormat() === 'url') {
					$this->setValue($image->getGuid());
				}
			}
		}
		
		return parent::_render();
	}
}
