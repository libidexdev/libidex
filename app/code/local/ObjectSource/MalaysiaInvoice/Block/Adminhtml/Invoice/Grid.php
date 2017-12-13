<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('os_malaysia_invoice');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('os_malaysia_invoice/invoice_collection');
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Entity ID'),
                'index' => 'entity_id',
                'type'  => 'number',
                'width' => '30px',
            ));

        $this->addColumn('invoice_reference',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Invoice Reference'),
                'index' => 'invoice_reference',
            ));

        $this->addColumn('awb_number',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Airway Bill Number'),
                'index' => 'awb_number',
            ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('os_malaysia_invoice')->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('total_gbp', array(
            'header' => Mage::helper('os_malaysia_invoice')->__('Total (GBP)'),
            'index' => 'malaysia_total_gbp',
            'type'  => 'currency',
            'currency_code' => 'GBP',
        ));

        $this->addColumn('total_usd', array(
            'header' => Mage::helper('os_malaysia_invoice')->__('Total (USD)'),
            'index' => 'malaysia_total_usd',
            'type'  => 'currency',
            'currency_code' => 'USD',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('os_malaysia_invoice')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('os_malaysia_invoice')->__('View'),
                        'url'     => array('base'=>'*/*/view'),
                        'field'   => 'invoice_id',
                        'data-column' => 'action',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'entity_id',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array(
                'invoice_id'=>$row->getId())
        );
    }
}
