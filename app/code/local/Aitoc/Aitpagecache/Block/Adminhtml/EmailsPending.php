<?php
class Aitoc_Aitpagecache_Block_Adminhtml_EmailsPending extends Aitoc_Aitpagecache_Block_Adminhtml_EmailsAbstract
{
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        $collection->getSelect()->where('sent_at IS NULL');
        return parent::_prepareCollection();
    }
}