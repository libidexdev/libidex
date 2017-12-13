<?php
/**
 * @category    Fishpig
 * @package     Fishpig_iBanners
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_iBanners_Adminhtml_iBannersController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->_title('iBanners');
		$this->_setActiveMenu('cms/ibanners');
		$this->renderLayout();
	}
	
	/**
	 * Display the group grid
	 *
	 */
	public function groupGridAction()
	{
		$this->getResponse()
			->setBody($this->getLayout()->createBlock('ibanners/adminhtml_group_grid')->toHtml());
	}
	
	
	/**
	 * Display the banner grid
	 *
	 */
	public function pageGridAction()
	{
		$this->getResponse()
			->setBody($this->getLayout()->createBlock('ibanners/adminhtml_banner_grid')->toHtml());
	}
	
	/**
	 * Display the Extend tab
	 *
	 * @return void
	 */
	public function extendAction()
	{
		$block = $this->getLayout()
			->createBlock('ibanners/adminhtml_extend')
			->setModule('Fishpig_iBanners')
			->setMedium('Add-On Tab')
			->setTemplate('large.phtml')
			->setLimit(4)
			->setPreferred(array(
				'Fishpig_Bolt',
				'Fishpig_Opti',
				'Fishpig_BasketShipping',
				'Fishpig_Wordpress',
				'Fishpig_CrossLink',
				'Fishpig_AttributeSplashPro',
				'Fishpig_NoBots'
			));
			
		$this->getResponse()
			->setBody(
				$block->toHtml()
			);
	}
}