<?php
class ObjectSource_MalaysiaInvoice_Block_Adminhtml_Invoice_Edit_Tab_Items extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('os_malaysia_invoice');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getInvoice()
    {
        return Mage::registry('invoice');
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Item_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_item_collection')
            ->addFieldToFilter('order_id', $this->_getInvoice()->getOrderIds());
        $collection->join(array('order' => 'sales/order'), 'main_table.order_id=order.entity_id', array('increment_id'));

        $this->_prepareCollectionItems($collection);
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('item_id',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Item ID'),
                'index' => 'entity_id',
                'type'  => 'checkbox',
                'width' => '50px',
                'filter'    => false,
                'align'     => 'center',
                'disabled'  => false,
                'disabled_values'   => array(),
                'field_name'  => 'item_id[]'
            ));
        $this->addColumn('increment_id',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Order ID'),
                'index' => 'increment_id',
                'filter'    => false,
            ));

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Product Name'),
                'index' => 'name',
                'filter'    => false,
            ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('SKU'),
                'index' => 'sku',
                'filter'    => false,
            ));

        $this->addColumn('color',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Colour'),
                'index' => 'color',
                'filter'    => false,
                'sortable'  => 0
            ));

        $this->addColumn('size',
            array(
                'header'=> Mage::helper('os_malaysia_invoice')->__('Size'),
                'index' => 'size',
                'filter' => false
            ));

        $this->addColumn('malaysia_price', array(
            'header' => Mage::helper('os_malaysia_invoice')->__(' Malaysia Price'),
            'index' => 'malaysia_price',
            'type'  => 'currency',
            'currency_code' => 'GBP',
            'filter'    => false,
        ));

        $this->addColumn('comment', array(
            'header'    => Mage::helper('os_malaysia_invoice')->__('Comment'),
            'index'     => 'comment',
            'renderer'      => 'os_malaysia_invoice_adminhtml/widget_grid_column_renderer_namedInput',
            'filter'    => false,
            'align'     => 'center',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Walks through items collection and process the item data.
     *
     * @param Mage_Sales_Model_Resource_Order_Item_Collection $collection
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected function _prepareCollectionItems(Mage_Sales_Model_Resource_Order_Item_Collection $collection)
    {
        foreach ($collection as $item) {
            if ($item->getParentItemId()) {
                $collection->removeItemByKey($item->getId());
                continue;
            }

            $this->_decodeCustomOptions($item);

            $malaysiaPrice = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue($item->getProductId(), 'malaysia_price', $item->getStoreId());
            $item->setMalaysiaPrice($malaysiaPrice);

            // split multiple quantities into separate items
            if (($qty = $item->getQtyOrdered()) > 1) {
                $item->setQtyOrdered(1);
                for ($i = 1; $i < $qty; $i++) {
                    $newItem = clone $item;
                    $newItem->setId($item->getId().  '_ ' . $i);
                    $collection->addItem($newItem);
                }
            }

        }

        return $collection;
    }

    /**
     * Decodes custom options from sales order item.
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _decodeCustomOptions(Mage_Sales_Model_Order_Item $item)
    {
        return Mage::helper('os_malaysia_invoice/customOption')->decodeCustomOptions($item);
    }

    /**
     * Disables sorting options.
     *
     * @return bool
     */
    public function getSortable()
    {
        return false;
    }

    /**
     * Disables main buttons.
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        return '';
    }
}
