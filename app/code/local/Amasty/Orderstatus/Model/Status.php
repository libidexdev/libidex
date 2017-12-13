<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderstatus
*/
class Amasty_Orderstatus_Model_Status extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderstatus/status');
    }
    
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->getAlias())
        {
            $this->setAlias($this->_generateAlias($this->getStatus()));
        }
        return $this;
    }
    
    protected function _generateAlias($title)
    {
        $alias = trim(strtolower(preg_replace('@[^A-Za-z0-9_]@', '', $title)));
        if (strlen($alias) > 30)
        {
            $alias = substr($alias, 0, 30);
        }
        if (!$alias)
        {
            $alias = uniqid(rand(10, 99));
        }
        return $alias;
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        Mage::getModel('amorderstatus/status_template')->attachTemplates($this);
        return $this;
    }
}