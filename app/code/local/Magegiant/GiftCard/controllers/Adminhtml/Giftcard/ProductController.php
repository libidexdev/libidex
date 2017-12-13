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

class Magegiant_GiftCard_Adminhtml_Giftcard_ProductController extends Mage_Adminhtml_Controller_Action
{
	public function newConditionHtmlAction()
	{
		$id      = $this->getRequest()->getParam('id');
		$typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
		$type    = $typeArr[0];

		$model = Mage::getModel($type)
			->setId($id)
			->setType($type)
			->setRule(Mage::getModel('giftcard/giftcard'))
			->setPrefix('conditions');
		if (!empty($typeArr[1])) {
			$model->setAttribute($typeArr[1]);
		}

		if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
			$model->setJsFormObject($this->getRequest()->getParam('form'));
			$html = $model->asHtmlRecursive();
		} else {
			$html = '';
		}
		$this->getResponse()->setBody($html);
	}

	public function newActionHtmlAction()
	{
		$id      = $this->getRequest()->getParam('id');
		$typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
		$type    = $typeArr[0];

		$model = Mage::getModel($type)
			->setId($id)
			->setType($type)
			->setRule(Mage::getModel('giftcard/giftcard'))
			->setPrefix('actions');
		if (!empty($typeArr[1])) {
			$model->setAttribute($typeArr[1]);
		}

		if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
			$model->setJsFormObject($this->getRequest()->getParam('form'));
			$html = $model->asHtmlRecursive();
		} else {
			$html = '';
		}
		$this->getResponse()->setBody($html);
	}
}