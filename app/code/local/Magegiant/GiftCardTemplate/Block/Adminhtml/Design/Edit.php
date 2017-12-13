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
 * Giftcardtemplate Edit Block
 *
 * @category     Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    const PAGE_TABS_BLOCK_ID = 'design_tabs';

    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'giftcardtemplate';
        $this->_controller = 'adminhtml_design';

        $this->_updateButton('save', 'label', Mage::helper('giftcardtemplate')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('giftcardtemplate')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
            'class'   => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('giftcardtemplate_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'giftcardtemplate_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'giftcardtemplate_content');
            }

            function saveAndContinueEdit(urlTemplate){
                var urlTemplateSyntax = /(^|.|\\r|\\n)({{(\\w+)}})/;
                var template = new Template(urlTemplate, urlTemplateSyntax);
                var url = template.evaluate({tab_id:" . self::PAGE_TABS_BLOCK_ID . "JsTabs.activeTab.id});
                editForm.submit(url);
            }
        ";
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('design_data')
            && Mage::registry('design_data')->getId()
        ) {
            return Mage::helper('giftcardtemplate')->__("Edit Topic #%s",
                $this->htmlEscape(Mage::registry('design_data')->getId())
            );
        }

        return Mage::helper('giftcardtemplate')->__('Add Topic');
    }
}