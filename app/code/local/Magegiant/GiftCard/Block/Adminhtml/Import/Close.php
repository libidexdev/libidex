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


class Magegiant_GiftCard_Block_Adminhtml_Import_Close extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('magegiant/giftcard/import/close.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('giftcard')->__('Close Window'),
                    'onclick' => 'closeWindow()'
                ))
        );
    }


    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }
}
