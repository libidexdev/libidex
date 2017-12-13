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
 * Giftcardtemplate Grid Block
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Block_Adminhtml_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('itemGrid');
        $this->setDefaultSort('item_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(false);
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('giftcardtemplate/design_items')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('item_id', array(
            'header' => Mage::helper('giftcardtemplate')->__('ID'),
            'align'  => 'right',
            'width'  => '50px',
            'index'  => 'item_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('giftcardtemplate')->__('Name'),
            'align'  => 'left',
            'index'  => 'name',
        ));
        $this->addColumn('format_id', array(
                'header'  => Mage::helper('giftcardtemplate')->__('Format'),
                'align'   => 'left',
                'index'   => 'format_id',
                'type'    => 'options',
                'options' => Mage::getSingleton('giftcardtemplate/format_options')->getOptionArray()
            )
        );
        $this->addColumn('thumb_file', array(
            'header'   => Mage::helper('giftcardtemplate')->__('Thumbnail'),
            'type'     => 'text',
            'renderer' => 'giftcardtemplate/adminhtml_design_edit_tab_renderer_image',
            'align'    => 'center',
            'width'    => '120px',
        ));
        $this->addColumn('status', array(
            'header'  => Mage::helper('giftcardtemplate')->__('Status'),
            'align'   => 'left',
            'index'   => 'status',
            'type'    => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
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
                        'url'     => array('base' => '*/*/edit'),
                        'field'   => 'id'
                    )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magegiant_GiftCardTemplate_Block_Adminhtml_Design_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('giftcardtemplate')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('giftcardtemplate')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('giftcardtemplate/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'      => Mage::helper('giftcardtemplate')->__('Change status'),
            'url'        => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'status',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('giftcardtemplate')->__('Status'),
                    'values' => $statuses
                ))
        ));

        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}