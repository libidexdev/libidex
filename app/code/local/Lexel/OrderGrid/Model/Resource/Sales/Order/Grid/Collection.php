<?php
    class Lexel_OrderGrid_Model_Resource_Sales_Order_Grid_Collection extends Mage_Sales_Model_Resource_Order_Grid_Collection
    {
        public function getSelectCountSql()
        {
            $countSelect = parent::getSelectCountSql();

            if (Mage::app()->getRequest()->getControllerName() == 'sales_order') {
                $countSelect->reset(Zend_Db_Select::GROUP);
                $countSelect->reset(Zend_Db_Select::COLUMNS);
                $countSelect->columns("COUNT(DISTINCT main_table.entity_id)");

                $havingCondition = $countSelect->getPart(Zend_Db_Select::HAVING);
                if (count($havingCondition)) {
                    $countSelect->where(
                        str_replace("sum(`sales_flat_order_item`.weight)", 'sales_flat_order_item.weight', $havingCondition[0])
                    );
                    $countSelect->reset(Zend_Db_Select::HAVING);
                }
            }

            return $countSelect;
        }
    }