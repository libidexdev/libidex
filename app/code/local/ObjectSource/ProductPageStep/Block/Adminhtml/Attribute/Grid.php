<?php
class ObjectSource_ProductPageStep_Block_Adminhtml_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('attributeGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('productpagestep/attribute')->getResourceCollection();
            
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('attribute_id', array(
            'header'    => Mage::helper('productpagestep')->__('ID'),
            'align'     => 'right',
            'width'     => '10px',
            'index'     => 'attribute_id',
        ));
        $this->addColumn('category', array(
            'header'    => Mage::helper('productpagestep')->__('Category'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'category',
        ));
        $this->addColumn('option_label', array(
            'header'    => Mage::helper('productpagestep')->__('Option Label'),
            'align'     => 'left',
            'width'     => '20px',
            'index'     => 'option_label',
        ));
        $this->addColumn('class', array(
            'header'    => Mage::helper('productpagestep')->__('Class'),
            'align'     => 'left',
            'width'     => '10px',
            'index'     => 'class',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}