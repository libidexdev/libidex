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
class Magegiant_GiftCardTemplate_Adminhtml_Giftcard_Template_ItemsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magegiant_GiftCardTemplate_Adminhtml_GiftcardtemplateController
     */
    protected function _initAction()
    {
        $this->_title($this->__('Giftcard Template'))->_title($this->__('Designs'));
        $this->loadLayout()
            ->_setActiveMenu('giftcard/giftcardtemplate')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
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
        $itemId = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('giftcardtemplate/design_items')->load($itemId);

        if ($model->getId() || $itemId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('item_data', $model);

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
            $this->_addContent($this->getLayout()->createBlock('giftcardtemplate/adminhtml_items_edit'))
                ->_addLeft($this->getLayout()->createBlock('giftcardtemplate/adminhtml_items_edit_tabs'));

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
            $model    = Mage::getModel('giftcardtemplate/design_items');
            $designId = $this->getRequest()->getParam('id');
            $upload   = Mage::helper('giftcardtemplate/upload');
            /*Process upload files*/
            if (isset($data['source_file']['delete'])) {
                $upload->deleteFile(('source_file'));
                $data['source_file'] = '';
            } else {
                if ($sourceFile = $upload->uploadFile('source_file')) {
                    $data['source_file'] = $sourceFile;
                    $data['is_default']  = 0;
                } else {
                    unset($data['source_file']);
                }
            }
            if (isset($data['thumb_file']['delete'])) {
                $upload->deleteFile(('thumb_file'));
                $data['thumb_file'] = '';
            } else {
                if ($thumbFile = $upload->uploadFile('thumb_file', 'thumbs')) {
                    $data['thumb_file'] = $thumbFile;
                } else {
                    unset($data['thumb_file']);
                }
            }
            /*Video Thumb*/
            if ($data['format_id'] == Magegiant_GiftCardTemplate_Model_Format_Options::FORMAT_ANIMATED) {
                $thumb              = Mage::helper('giftcardtemplate')->getVideoThumbFromUrl($data['video_url']);
                $data['thumb_file'] = $thumb;
            }
            /*---End Upload Files---*/
            $model->load($designId)->addData($data);
            try {
                $model->save();
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
                $model = Mage::getModel('giftcardtemplate/design_items');
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
        $itemIds = $this->getRequest()->getParam('item');
        if (!is_array($itemIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    $giftcardtemplate = Mage::getModel('giftcardtemplate/design_items')->load($itemId);
                    $giftcardtemplate->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($itemIds))
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
        $itemIds = $this->getRequest()->getParam('item');
        if (!is_array($itemIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    Mage::getSingleton('giftcardtemplate/design_items')
                        ->load($itemId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($itemIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftcard/giftcardtemplate/items');
    }
}