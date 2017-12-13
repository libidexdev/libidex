<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ZeroSellers
 */
class Amasty_ZeroSellers_Model_Command_Abstract
{ 
    protected $_type       = '';
    protected $_label      = '';
    protected $_fieldLabel = '';
    
    protected $_errors    = array();    
    
    public function __construct($type='')
    {
        $this->_type = $type;
    }
    
    /**
     * Factory method. Creates a new command object by its type
     *
     * @param string $type command type
     * @return Amasty_ZeroSellers_Model_Command_Abstract
     */
    public static function factory($type)
    {
        $className = 'Amasty_ZeroSellers_Model_Command_' . ucfirst($type);
        return new $className($type);
    }
    
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @throws Exception
     * @return string success message if any
     */
    public function execute($ids, $storeId, $val)
    {
        $this->_errors = array();
        
        $hlp = Mage::helper('amzerosellers');
        if (!is_array($ids)) {
            throw new Exception($hlp->__('Please select product(s)')); 
        }
        if (!strlen($val)) {
            throw new Exception($hlp->__('Please provide the value for the action')); 
        }                  
               
        return '';
    }
    
    /**
     * Adds the command label to the mass actions list
     *
     * @param Amasty_ZeroSellers_Block_Adminhtml_Purchased_Grid $grid
     * @return Amasty_ZeroSellers_Model_Command_Abstract
     */
    public function addAction($block)
    {
        $hlp = Mage::helper('amzerosellers');
        $storeId = intVal(Mage::app()->getRequest()->getParam('store'));

        $params = array(
            'label'      => $hlp->__($this->_label),
            'url'        => $block->getParentBlock()->getUrl('adminhtml/amzerosellers/do/command/' . $this->_type . '/store/' . $storeId),
        );
        if($this->_fieldLabel) {
            $params['additional'] = $this->_getValueField($hlp->__($this->_fieldLabel));
        }
        $block->addItem('ampzerosellers_' . $this->_type, $params);
        return $this;         
    }    
    
    /**
     * Returns value field options for the mass actions block
     *
     * @param string $title field title
     * @return array
     */
    protected function _getValueField($title)
    {
        $field = array('amzerosellers_value' => array(
            'name'  => 'amzerosellers_value',
            'type'  => 'text',
            'class' => 'required-entry',
            'label' => $title,
        )); 
        return $field;       
    }
    
    /**
     * Gets list of not critical errors after the command execution
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;       
    }   
    
    public function getLabel()
    {
        return $this->_label;       
    }   

}