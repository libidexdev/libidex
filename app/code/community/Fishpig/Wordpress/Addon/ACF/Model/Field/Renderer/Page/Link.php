<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Page_Link extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
		if ($value = $this->getValue()) {
			$this->setValue(false);
			
			if (is_array($value)) {
				$pages = Mage::getResourceModel('wordpress/page_collection')
					->addIsPublishedFilter()
					->setOrderByPostDate()
					->addFieldToFilter('main_table.ID', array('IN' => $value));
				
				$links = array();
				
				foreach($pages as $page)	{
					$links[] = $page->getUrl();
				}
				
				if (count($links) > 0) {
					$this->setValue($links);
				}
			}
			else {
				$page = Mage::getModel('wordpress/page')->load((int)$value);
			
				if ($page->getId()) {
					$this->setValue($page->getUrl());
				}
			}
		}
		
		return parent::_render();
	}
}
