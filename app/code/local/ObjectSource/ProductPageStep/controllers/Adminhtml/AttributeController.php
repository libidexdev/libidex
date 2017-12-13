<?php
class ObjectSource_ProductPageStep_Adminhtml_AttributeController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/attributes/productpagestep');
        $this->_addContent($this->getLayout()->createBlock('productpagestep/adminhtml_attribute'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('catalog/attributes/productpagestep');
        $this->_addContent($this->getLayout()->createBlock('productpagestep/adminhtml_attribute_edit'));
        $this->renderLayout();
    }

    public function editAction() 
    {
        $attributeId     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('productpagestep/attribute')->load($attributeId);

        Mage::register('productpagestep_attribute', $model);

        $this->loadLayout();

        $this->_setActiveMenu('catalog/attributes/productpagestep');
        $this->_addContent($this->getLayout()->createBlock('productpagestep/adminhtml_attribute_edit'));
        $this->renderLayout();
    }

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('productpagestep/attribute');
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model->setData($data);
            $model->setId($id);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $msg = Mage::helper('productpagestep')->__('Attribute properties have been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);

                $this->_redirect('*/*/');
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productpagestep')->__('Unable to find a attribute to save'));
        $this->_redirect('*/*/');
    }
}