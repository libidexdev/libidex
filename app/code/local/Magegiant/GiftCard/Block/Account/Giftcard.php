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
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

class Magegiant_GiftCard_Block_Account_Giftcard extends Magegiant_GiftCard_Block_Abstract
{
	protected $_buttons = array();

	public function addButton($name, $path, $label, $type, $position = 100)
	{
		$button = new Varien_Object(array(
			'name'     => $name,
			'path'     => $path,
			'label'    => $label,
			'type'     => $type,
			'position' => $position,
			'url'      => $this->getUrl($path)
		));

		$this->_buttons[$name] = $button;

		return $this;
	}

	public function removeButton($name)
	{
		if (isset($this->_buttons[$name])) {
			unset($this->_buttons[$name]);
		}

		return $this;
	}

	public function getButtons()
	{
		usort($this->_buttons, function ($a, $b) {
			return $a->getPosition() - $b->getPosition();
		});

		return $this->_buttons;
	}
}