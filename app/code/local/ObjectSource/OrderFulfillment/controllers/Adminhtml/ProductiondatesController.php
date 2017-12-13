<?php
class ObjectSource_OrderFulfillment_Adminhtml_ProductiondatesController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/orderfulfillment');
        $this->_addContent($this->getLayout()->createBlock('orderfulfillment/adminhtml_productiondates'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('sales/productiondates');
        $this->_addContent($this->getLayout()->createBlock('orderfulfillment/adminhtml_productiondates_edit'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $attributeId     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('orderfulfillment/productiondates')->load($attributeId);

        Mage::register('orderfulfillment_productiondates', $model);

        $this->loadLayout();

        $this->_setActiveMenu('sales/productiondates');
        $this->_addContent($this->getLayout()->createBlock('orderfulfillment/adminhtml_productiondates_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('orderfulfillment/productiondates');
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model->setData($data);
            $model->setId($id);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $msg = Mage::helper('orderfulfillment')->__('Production date properties have been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);

                $this->_redirect('*/*/');

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orderfulfillment')->__('Unable to find a production date to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model  = Mage::getModel('orderfulfillment/productiondates');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orderfulfillment')->__('Production date has been deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
}