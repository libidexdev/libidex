<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field extends Fishpig_Wordpress_Model_Abstract
{
	/**
	 * Initialise the model
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_acf/field');
	}

	/**
	 * Renders the initial meta value as it's ACF value
	 *
	 * @return false|mixed
	 */
	public function render()
	{
		if ($renderer = $this->_getRenderer($this->getType())) {
			return $renderer->setField($this)->setPost($this->getPost())->setValue($this->getValue())->render();
		}
		
		return false;
	}
	
	/**
	 * Retrieves the rendering class
	 * If class based on $type isn't found, returns default renderer
	 *
	 * @param string $type
	 * @return Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
	 */
	protected function _getRenderer($type)
	{
		$types = array($type, 'default');
		$baseDir = Mage::getModuleDir('', 'Fishpig_Wordpress_Addon_ACF') . DS . 'Model' . DS . 'Field' . DS . 'Renderer' . DS;

		foreach($types as $type) {
			$classFile = $baseDir . uc_words($type, DS) . '.php';
			
			if (is_file($classFile) && ($renderer = Mage::getModel('wp_addon_acf/field_renderer_' . $type)) !== false) {
				return $renderer->setScope($this->getScope());
			}
		}
		
		return false;
	}
}
