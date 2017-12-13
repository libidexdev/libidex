<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Lexel_OrderGrid_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareColumns ()
    {
        parent::_prepareColumns();

        $this->addColumn('weight', array (
            'header' => Mage::helper('sales')->__('Weight'),
            'filter' => false,
            'index' => 'weight',
            'type' => 'text',
            'width' => '150px',
        ));

        $this->addColumn('country_id', array (
            'header' => Mage::helper('sales')->__('Country'),
            'index' => 'country_id',
            'type' => 'text',
            'width' => '150px',
        ));

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->join(
            'sales/order_item', '`sales/order_item`.order_id=`main_table`.entity_id',
                array('weight'=> new Zend_Db_Expr('sum(`sales/order_item`.weight)')))

            ->join('sales/order_address',
            '`sales/order_address`.parent_id = `main_table`.entity_id AND `sales/order_address`.address_type = "billing"',
            array('country_id'))

            ->join(array('order' => 'sales/order'),
            'main_table.entity_id = order.entity_id',
            array('coupon_code'));

        $collection->addFilterToMap('store_id', 'main_table.store_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('increment_id', 'main_table.increment_id');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('grand_total', 'main_table.grand_total');
        $collection->addFilterToMap('base_grand_total', 'main_table.base_grand_total');

        $collection->getSelect()->group('main_table.entity_id');
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function filterRapidOrder(Mage_Sales_Model_Resource_Order_Grid_Collection $collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addFieldToFilter('order.coupon_code', array('like' => "%$value%"));
    }

    public function getRapidOrderOptions()
    {
        return array(
            'RAPID' => 'All Rapid Orders' ,
            'RAPIDPLATINUM' => 'Only platinum' ,
            'RAPIDGOLD' => 'Only gold' ,
            'RAPIDSILVER' => 'Only silver'
        );
    }
}
