<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Conf_Model_Source_Reload extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amconf');
		return array(
			array('value' => 'name', 'label' => $hlp->__('Name')),
            array('value' => 'description',  'label' => $hlp->__('Description')),
            array('value' => 'image',  'label' => $hlp->__('Image')),
            array('value' => 'short_description',  'label' => $hlp->__('Short Description')),
			array('value' => 'attributes',  'label' => $hlp->__('Attributes block')),
		);
	}
	
}