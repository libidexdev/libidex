<?php
class Lexel_InventoryReport_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_exportPageSize = 500;

    protected $_itemCollectionModel;

    protected $_result;

    protected $_countTotals = true;

    public function __construct() {
        parent::__construct();

        $this->setId('inventoryReportGrid');//set id of grid
        $this->setDefaultSort('quantity');//set default sort by field
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('product_filter');
    }


    public function getCsvFileE() {
        $this->_isExport = true;
        $this->_prepareGrid();

        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        while (file_exists($file)) {
            sleep(1);
            $name = md5(microtime());
            $file = $path . DS . $name . '.csv';
        }

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getExportHeaders());

        $this->_exportIterateCollectionEnhanced('_exportCsvItem', array($io));

        if ($this->getCountTotals()) {
            $io->streamWriteCsv($this->_getExportTotals());
        }

        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }

    public function _exportIterateCollectionEnhanced($callback, array $args)
    {
        $originalCollection  = $this->getCollection();
        $count               = null;
        $page                = 1;
        $ourLastPage         = 171;
        $iPage               = null;
        $break               = false;

        $this->setDefaultLimit(3415);

        while ($break !== true) {
            $collection = clone $originalCollection;

            $collection->setPageSize($this->_exportPageSize);
            $collection->setCurPage($page);
            $collection->load(true, true);

            if (is_null($count)) {
                $count = $collection->getSize();
            }

            if ($ourLastPage == $page) {
                $break = true;
            }

            $page++;

            foreach ($collection as $item) {
                call_user_func_array(array($this, $callback), array_merge(array($item), $args));
            }
        }
    }



    protected function _prepareCollection()
    {
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('type_id')
            ->addAttributeToFilter('sku', array('like' => 'LEX-%'));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumn('name',
            array(
                'header' => Mage::helper('catalog')->__('Name'),
                'index'  => 'name'
            )
        );

        $this->addColumn('sku',
            array(
                'header' => Mage::helper('catalog')->__('Sku'),
                'index'  => 'sku'
            )
        );

        $this->addColumn('qty',
            array(
                'header' => Mage::helper('catalog')->__('Current Stock'),
                'index'  => 'qty',
                'type'   => 'number'
            )
        );

        $this->addColumn('unit_value',
            array(
                'header'   => Mage::helper('catalog')->__('Unit Value'),
                'index'    => 'price',
                'type'     => 'price',
                'currency_code' => 'GBP'
            )
        );
        
        $this->addColumn('total_remaining',
            array(
                'header'   => Mage::helper('catalog')->__('Item Stock Value'),
                'index'    => 'total_remaining',
                'renderer' => 'Lexel_InventoryReport_Block_Adminhtml_Render_TotalValue'
            )
        );
        
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        //$this->addExportType('*/*/exportCsvE', Mage::helper('sales')->__('CSVe'));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/inventoryreport/*', array('_current' => true));
    }
    
    public function getTotals()
    {
        $totals = new Varien_Object();
        $fields = array(
            'qty' => 0,
            'total_remaining' => 0
        );
        foreach ($this->getCollection() as $item) {
            foreach($fields as $field=>$value){
                if ($field == 'total_remaining') {
                    $qty = $item->getQty();
                    $val = $item->getPrice();
                    $sub = $qty * $val;
                    $fields['total_remaining'] += $sub;
                } else {
                    $fields[$field] += $item->getData($field);
                }
            }
        }
        //First column in the grid
        $fields['name']='Totals';
        $totals->setData($fields);
        return $totals;
    }
}