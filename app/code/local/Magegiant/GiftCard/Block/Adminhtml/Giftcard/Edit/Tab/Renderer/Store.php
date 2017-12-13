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

class Magegiant_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Renderer_Store
	extends Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element
{
	protected function _construct()
	{
		$this->setTemplate('magegiant/giftcard/form/renderer/store.phtml');
	}

	public function getStoreSelectOptions()
	{
		$giftcard = Mage::registry('current_giftcard');

		$curWebsite = $giftcard->getWebsiteId();
		$curStore   = $giftcard->getStoreId();

		$storeModel = Mage::getSingleton('adminhtml/system_store');
		/* @var $storeModel Mage_Adminhtml_Model_System_Store */

		$options = array();

		foreach ($storeModel->getWebsiteCollection() as $website) {
			$websiteShow = false;
			foreach ($storeModel->getGroupCollection() as $group) {
				if ($group->getWebsiteId() != $website->getId()) {
					continue;
				}
				$groupShow = false;
				foreach ($storeModel->getStoreCollection() as $store) {
					if ($store->getGroupId() != $group->getId()) {
						continue;
					}
					if (!$websiteShow) {
						$websiteShow                               = true;
						$options['website_' . $website->getCode()] = array(
							'label'    => $website->getName(),
							'selected' => false,
							'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
							'disabled' => true
						);
					}
					if (!$groupShow) {
						$groupShow                                     = true;
						$options['group_' . $group->getId() . '_open'] = array(
							'is_group' => true,
							'is_close' => false,
							'label'    => $group->getName(),
							'style'    => 'padding-left:32px;'
						);
					}
					$options[$store->getId()] = array(
						'label'    => $store->getName(),
						'selected' => $curStore == $store->getId(),
						'style'    => '',
						'disabled' => false
					);
				}
				if ($groupShow) {
					$options['group_' . $group->getId() . '_close'] = array(
						'is_group' => true,
						'is_close' => true,
					);
				}
			}
		}

		return $options;
	}
}
