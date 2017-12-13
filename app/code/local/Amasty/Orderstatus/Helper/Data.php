<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderstatus
*/
class Amasty_Orderstatus_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSystemStatuses()
    {
        $statusList = array();
        $statuses = Mage::getConfig()->getNode('global/sales/order/statuses')->asArray();
        foreach ($statuses as $alias => $data)
        {
            if (!isset($data['@']['amorderstatus']))
            {
                $statusList[$data['label']] = array(
                    'custom_name'   => Mage::getModel('amorderstatus/status')->load($alias, 'alias')->getStatus(),
                    'alias'         => $alias,
                );
            }
        }
        return $statusList;
    }
}