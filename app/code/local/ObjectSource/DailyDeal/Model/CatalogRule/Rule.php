<?php
class ObjectSource_DailyDeal_Model_CatalogRule_Rule extends Mage_CatalogRule_Model_Rule
{
    protected $_promotionSuffix;
    
    public function __construct(array $args)
    {
        parent::__construct($args);
        
        $this->_promotionSuffix = Mage::getStoreConfig('dailydeal/dailydeal_group/prom_suffix');
    }

    public function getDailydealPromotionsCollection($promotionStoreId)
    {
        $promotions = Mage::getModel('catalogrule/rule')->getCollection()
            ->addFieldToFilter('name', array('like'=>$this->_promotionSuffix.'%'));
        $promotions->getSelect()->join(array('website'=>'catalogrule_website'),
            "main_table.rule_id=website.rule_id AND website.website_id=$promotionStoreId",array('website_id'));
        $promotions->setOrder('rule_id', 'ASC');
        return $promotions;
    }

    public function getActiveDailydealPromotions()
    {
        $promotionStoreId = Mage::app()->getStore()->getStoreId();
        $currentDate = date('Y-m-d');
        $promotions = Mage::getModel('catalogrule/rule')->getDailydealPromotionsCollection($promotionStoreId)
            ->addFieldToFilter('from_date', array('lteq' => $currentDate))
            ->addFieldToFilter('to_date', array('gteq' => $currentDate));

        return $promotions;
    }

    public function getDailydealPromotionProduct()
    {
        $conditions = unserialize($this->getConditionsSerialized());
        $sku = $conditions['conditions'][0]['value'];
        return Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
    }
}
