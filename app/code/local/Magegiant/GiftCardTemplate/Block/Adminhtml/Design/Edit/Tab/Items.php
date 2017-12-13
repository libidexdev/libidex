<?php

/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magegiant.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement.html
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Edit_Tab_Items extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('detailGrid');
        $this->setDefaultSort('detail_id');
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('giftcardtemplate/design_items_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'selected_items',
            array(
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'selected_items',
                'values'           => $this->_getSelectedItems(),
                'align'            => 'center',
                'field_name'       => 'selected_items[]',
                'index'            => 'item_id',
                'width'            => '15px',
            )
        );
        $this->addColumn('item_id', array(
            'header' => Mage::helper('giftcardtemplate')->__('Item ID'),
            'type'   => 'text',
            'index'  => 'item_id',
            'width'  => '25px',
        ));
        $this->addColumn('item_name', array(
            'header' => Mage::helper('giftcardtemplate')->__('Name'),
            'type'   => 'text',
            'index'  => 'name'
        ));
        $this->addColumn('thumb_file', array(
            'header'   => Mage::helper('giftcardtemplate')->__('Thumbnail'),
            'type'     => 'text',
            'index'    => 'thumb_file',
            'renderer' => 'giftcardtemplate/adminhtml_design_edit_tab_renderer_image',
            'align'    => 'center',
            'width'    => '120px',
            'filter'   => false
        ));
        $this->addColumn('item_status', array(
            'header'  => Mage::helper('giftcardtemplate')->__('Status'),
            'align'   => 'left',
            'width'   => '80px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('sort_order', array(
            'header'   => Mage::helper('giftcardtemplate')->__('Sort Order'),
            'index'    => 'detail.sort_order',
            'width'    => '80px',
            'align'    => 'right',
            'renderer' => 'giftcardtemplate/adminhtml_design_edit_tab_renderer_sortOrder',
            'editable' => true,
        ));
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('giftcardtemplate')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('giftcardtemplate')->__('Edit'),
                        'url'     => array('base' => 'adminhtml/giftcard_template_items/edit'),
                        'target'  => '_blank',
                        'field'   => 'id'
                    )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    protected function _getSelectedItems()
    {
        return $this->getRequest()->getPost('selected_items', array());
    }

    public function getItemsJson()
    {
        $ids      = array();
        $designId = $this->getRequest()->getParam('id');
        if ($designId) {
            $details = Mage::getModel('giftcardtemplate/design_items_detail')->getCollection()
                ->addFieldToFilter('design_id', $designId);
            foreach ($details as $detail) {
                $ids[] = $detail->getItemId();
            }
            $itemsArray = array();
            foreach ($ids as $item) {
                $itemsArray[$item] = true;
            }
            if (sizeof($itemsArray))
                return Zend_Json::encode($itemsArray, false, array());

        }

        return '{}';
    }

    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/itemsGrid', array(
                '_current' => true
            ));
    }
}