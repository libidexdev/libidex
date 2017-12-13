<?php

/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCard
 * @author      Magegiant Developer
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

class Magegiant_GiftCardCredit_Block_Adminhtml_Credit_Renderer_Customer
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Render customer info to grid column html
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		$actionName = $this->getRequest()->getActionName();
		$column     = $this->getColumn()->getIndex();
		if (strpos($actionName, 'export') === 0) {
			return $row->getData($column);
		}

		return sprintf('<a target="_blank" href="%s">%s</a>',
			$this->getUrl('adminhtml/customer/edit', array('id' => $row->getData($column))),
			$row->getData('email')
		);
	}
}
