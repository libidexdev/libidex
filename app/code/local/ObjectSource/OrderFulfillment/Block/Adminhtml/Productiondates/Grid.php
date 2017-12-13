<?php
class ObjectSource_OrderFulfillment_Block_Adminhtml_Productiondates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productiondatesGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('orderfulfillment/productiondates')->getResourceCollection();
            
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('label', array(
            'header'    => Mage::helper('orderfulfillment')->__('Label'),
            'align'     => 'left',
            'width'     => '250px',
            'index'     => 'label',
        ));

        $this->addColumn('processing_total_from', array(
            'header'    => Mage::helper('orderfulfillment')->__('Processing Total From'),
            'align'     => 'right',
            'width'     => '10px',
            'index'     => 'processing_total_from',
        ));

        $this->addColumn('processing_total_to', array(
            'header'    => Mage::helper('orderfulfillment')->__('Processing Total To'),
            'align'     => 'right',
            'width'     => '10px',
            'index'     => 'processing_total_to',
        ));

        $this->addColumn('delayed', array(
            'header'    => Mage::helper('orderfulfillment')->__('Delayed (in days)'),
            'align'     => 'right',
            'width'     => '10px',
            'index'     => 'delayed',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}