<?php
/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * Giftcardtemplate Adminhtml Controller
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCardTemplate
 * @author      Magegiant Developer
 */
class Magegiant_GiftCardTemplate_Adminhtml_Giftcard_Template_DesignController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magegiant_GiftCardTemplate_Adminhtml_GiftcardtemplateController
     */
    protected function _initAction()
    {
        $this->_title($this->__('Giftcard Template'))->_title($this->__('Topics'));
        $this->loadLayout()
            ->_setActiveMenu('giftcard/giftcardtemplate')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Topic Manager'),
                Mage::helper('adminhtml')->__('Topic Manager')
            );

        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $designId = $this->getRequest()->getParam('id');
        $model    = Mage::getModel('giftcardtemplate/design')->load($designId);

        if ($model->getId() || $designId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('design_data', $model);

            $this->_initAction();
            if ($model->getId()) {
                $this->_title($model->getName());
            } else {
                $this->_title($this->__('Add new'));
            }
            $this->_setActiveMenu('giftcard/giftcardtemplate');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Design Manager'),
                Mage::helper('adminhtml')->__('Design Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Design News'),
                Mage::helper('adminhtml')->__('Design News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('giftcardtemplate/adminhtml_design_edit'))
                ->_addLeft($this->getLayout()->createBlock('giftcardtemplate/adminhtml_design_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('giftcardtemplate')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model    = Mage::getModel('giftcardtemplate/design');
            $designId = $this->getRequest()->getParam('id');
            $model->load($designId)->addData($data);
            try {
                $model->save();
                $designId      = $model->getId();
                $selectedItems = explode(',', $data['item_ids'] ? $data['item_ids'] : array());
                /*Insert Selected Item*/
                foreach ($selectedItems as $item) {
                    $sortOrder   = $data['sort_order'][$item];
                    $detailModel = Mage::getModel('giftcardtemplate/design_items_detail');
                    $existItem   = $detailModel->getCollection()
                        ->addFieldToFilter('design_id', $designId)
                        ->addFieldToFilter('item_id', $item)
                        ->getFirstItem();
                    if ($existItem && $existItem->getId()) {
                        $detailModel->load($existItem->getId())
                            ->setDesignId($designId)
                            ->setItemId($item)
                            ->setSortOrder($sortOrder);
                    } else {
                        $detailModel
                            ->setDesignId($designId)
                            ->setItemId($item)
                            ->setSortOrder($sortOrder);
                    }
                    $detailModel->save();
                }
                /*Delete Selected Item If Not Exist*/
                $detailCollection = Mage::getModel('giftcardtemplate/design_items_detail')->getCollection()
                    ->addFieldToFilter('design_id', $designId)
                    ->addFieldToFilter('item_id', array('nin' => $selectedItems));
                foreach ($detailCollection as $detail) {
                    $detail->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('giftcardtemplate')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('giftcardtemplate')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    public function itemsGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('giftcardtemplate/adminhtml_design_edit_tab_items')->toHtml()
        );
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('giftcardtemplate/design');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Design was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $designIds = $this->getRequest()->getParam('design');
        if (!is_array($designIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($designIds as $designId) {
                    $giftcardtemplate = Mage::getModel('giftcardtemplate/design')->load($designId);
                    $giftcardtemplate->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($designIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $designIds = $this->getRequest()->getParam('design');
        if (!is_array($designIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($designIds as $designId) {
                    Mage::getSingleton('giftcardtemplate/design')
                        ->load($designId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($designIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftcard/giftcardtemplate/designs');
    }
}