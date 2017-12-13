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
 * @package     Magegiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardtemplate Edit Tabs Block
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Items_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('item_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftcardtemplate')->__('Design Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => Mage::helper('giftcardtemplate')->__('General'),
            'title'   => Mage::helper('giftcardtemplate')->__('General'),
            'content' => $this->getLayout()
                ->createBlock('giftcardtemplate/adminhtml_items_edit_tab_general')
                ->toHtml(),
        ));
        $this->_updateActiveTab();

        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}