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
 * @package     Magegiant_GiftCardCredit
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardcredit Adminhtml Controller
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardCredit
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardCredit_Adminhtml_Giftcard_CreditController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('giftcard/credit')
			->_addBreadcrumb(
				$this->__('Credit Manager'),
				$this->__('Credit Manager')
			);

		$this->_title($this->__('Gift Card Credit'))->_title('Gift Card Credit');

		return $this;
	}

	/**
	 * index action
	 */
	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}

	/**
	 * export grid item to CSV type
	 */
	public function exportCsvAction()
	{
		$fileName = 'giftcardcredit.csv';
		$content  = $this->getLayout()
			->createBlock('giftcardcredit/adminhtml_credit_grid')
			->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}

	/**
	 * export grid item to XML type
	 */
	public function exportXmlAction()
	{
		$fileName = 'giftcardcredit.xml';
		$content  = $this->getLayout()
			->createBlock('giftcardcredit/adminhtml_credit_grid')
			->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}

	protected function _construct()
	{
		$this->setUsedModuleName('Magegiant_GiftCardCredit');
	}


	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('giftcardcredit');
	}
}