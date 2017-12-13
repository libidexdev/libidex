<?php
$this->startSetup();

$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('store_id', array('gt'=>0))->getAllIds();

foreach ($stores as $store)
{
    $data = array(
        'product_ids' => null,
        'name' => sprintf('Rapid Processing for Increased Shopping Basket Price (Store '.$store.')'),
        'description' => null,
        'is_active' => 1,
        'website_ids' => array($store),
        'customer_group_ids' => array(0,1,2,3),
        'coupon_type' => 2,
        'coupon_code' => 'RAPID'.$store,
        'from_date' => null,
        'to_date' => null,
        'sort_order' => null,
        'is_rss' => 0,
        'rule' => array(
            'conditions' => array(
                array(
                    'type' => 'salesrule/rule_condition_combine',
                    'aggregator' => 'all',
                    'value' => 1,
                    'new_child' => null
                )
            )
        ),
        'simple_action' => 'by_percent',
        'discount_amount' => -50,
        'discount_qty' => 0,
        'discount_step' => null,
        'apply_to_shipping' => 0,
        'simple_free_shipping' => 0,
        'stop_rules_processing' => 0,
        'rule' => array(
            'actions' => array(
                array(
                    'type' => 'salesrule/rule_condition_product_combine',
                    'aggregator' => 'all',
                    'value' => 1,
                    'new_child' => null
                )
            )
        ),
        'store_labels' => array('Rapid Processing')
    );

    $model = Mage::getModel('salesrule/rule');

    if (isset($data['rule']['conditions'])) {
        $data['conditions'] = $data['rule']['conditions'];
    }

    if (isset($data['rule']['actions'])) {
        $data['actions'] = $data['rule']['actions'];
    }

    unset($data['rule']);

    $model->loadPost($data);
    $model->save();
}

$this->endSetup(); 