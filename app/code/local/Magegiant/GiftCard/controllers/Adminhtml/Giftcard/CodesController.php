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
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */
class Magegiant_GiftCard_Adminhtml_Giftcard_CodesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magegiant_GiftCard_Adminhtml_GiftcardController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('giftcard/giftcard')
            ->_addBreadcrumb(
                $this->__('Manage Gift Codes'),
                $this->__('Manage Gift Codes')
            );
        $this->_title($this->__('Gift Card'))->_title('Gift Codes');

        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('giftcard/giftcard');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__('This gift card no longer exists.'));
                $this->_redirect('*/*');

                return;
            }
        }


        $data = $this->_getSession()->getGiftcardData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::dispatchEvent('magegiant_giftcard_adminhtml_edit', array(
            'giftcard' => $model
        ));

        Mage::register('current_giftcard', $model);
        $this->_initAction();
        if ($model->getId()) {
            $this->_title($this->__('Edit %s', $model->getCode()));
        } else {
            $this->_title($this->__('Add New'));
        }
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
			$data  = $this->_filterDates($data, array('expired_at'));

            $model = Mage::getModel('giftcard/giftcard');
            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
                if ($id != $model->getId()) {
                    Mage::throwException($this->__('Wrong gift card specified.'));
                }
            }

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (isset($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            }
            unset($data['rule']);

            $model->loadPost($data);

            $this->_getSession()->setGiftcardData($model->getData());

            try {
                $qty = $model->getQty();
                if ($qty > 1) {
                    $giftCards = $model->createMultiple();

                    $this->_getSession()->addSuccess(Mage::helper('giftcard')->__('The gift cards have been generated successfully.'));
                    $this->_getSession()->setGiftcardData(false);

                    if ($qty <= 10 && $this->getRequest()->getParam('action')) {
                        foreach ($giftCards as $card) {
                            $card->setSendBy(Mage::getSingleton('admin/session')->getUser()->getUsername())
                                ->sendEmail();
                        }
                    }
                } else {
                    $model->save();

                    $this->_getSession()->addSuccess(Mage::helper('giftcard')->__('The gift card has been successfully saved.'));
                    $this->_getSession()->setGiftcardData(false);

                    if ($this->getRequest()->getParam('action')) {
                        $model->setSendBy(Mage::getSingleton('admin/session')->getUser()->getUsername())
                            ->sendEmail();
                    }

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));

                        return;
                    }
                }

                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());

                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_getSession()->setGiftcardData(false);
                    $this->_redirect('*/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('*/*/new');
                }

                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('giftcard/giftcard');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Gift Card was successfully deleted')
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
        $giftcardIds = $this->getRequest()->getParam('giftcard');
        if (!is_array($giftcardIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select gift card(s)'));
        } else {
            $success = 0;
            foreach ($giftcardIds as $giftcardId) {
                try {
                    $giftcard = Mage::getModel('giftcard/giftcard')->load($giftcardId);
                    $giftcard->delete();
                    $success++;
                } catch (Exception $e) {

                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    $success)
            );
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStateAction()
    {
        $giftcardIds = $this->getRequest()->getParam('giftcard');
        if (!is_array($giftcardIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            $success = 0;
            $active  = $this->getRequest()->getParam('active');
            foreach ($giftcardIds as $giftcardId) {
                try {
                    $giftcard = Mage::getSingleton('giftcard/giftcard')->load($giftcardId);
                    $giftcard->setActive($active)
                        ->save();
                    $success++;
                } catch (Exception $e) {

                }
            }
            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) were successfully updated', $success)
            );
        }
        $this->_redirect('*/*/index');
    }

    public function massEmailAction()
    {
        $giftcardIds = $this->getRequest()->getParam('giftcard');
        if (!is_array($giftcardIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select gift card(s)'));
        } else {
            $success = 0;
            foreach ($giftcardIds as $giftcardId) {
                try {
                    $giftcard = Mage::getSingleton('giftcard/giftcard')->load($giftcardId);
                    $giftcard->sendEmail();
                    $success++;
                } catch (Exception $e) {

                }
            }
            $this->_getSession()->addSuccess(
                $this->__('Total of %d email(s) were successfully sent', $success)
            );
        }
        $this->_redirect('*/*/index');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function historygridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function importAction()
    {
        $this->_title($this->__('Import Gift Card'));

        $this->loadLayout('popup');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    public function importPostAction()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_file']['tmp_name'])) {
            try {
                $csvObject = new Varien_File_Csv();
                $csvData   = $csvObject->getData($_FILES['import_file']['tmp_name']);

                $fields     = array();
                $codes      = array();
                $csvTmpData = array();
                foreach ($csvData as $data) {
                    if (empty($fields)) {
                        $fields = $data;
                        continue;
                    }

                    $tmpData = array_combine($fields, $data);
                    if (isset($tmpData['Code']) && $tmpData['Code']) {
                        $codes[]      = $tmpData['Code'];
                        $csvTmpData[] = $tmpData;
                    }
                }

                $giftCardModel = Mage::getModel('giftcard/giftcard');

                $giftCard = $giftCardModel->getCollection()
                    ->addFieldToFilter('code', array('in' => $codes));
                $codes    = $giftCard->getSize() ? $giftCard->getColumnValues('code') : array();

                $statusLabel  = array_flip($giftCardModel->getStatusArray());
                $activeLabel  = array('Yes' => 1, 'No' => 0, 'Active' => 1, 'Inactive' => 0);
                $websiteLabel = array_flip(Mage::getModel('adminhtml/system_store')->getWebsiteOptionHash());
                $importData   = array();
                foreach ($csvTmpData as $key => $giftCardData) {
                    if (in_array($giftCardData['Code'], $codes)) {
                        continue;
                    }

                    if (!($status = $this->getValueFromLabel('Status', $giftCardData, $statusLabel))) {
                        continue;
                    }
                    if (!($active = $this->getValueFromLabel('Active', $giftCardData, $activeLabel))) {
                        continue;
                    }
                    if (!($websiteId = $this->getValueFromLabel('Website', $giftCardData, $websiteLabel))) {
                        continue;
                    }
                    if (isset($giftCardData['Balance'])) {
                        $dataBalance = preg_replace('/[^0-9,.]/', '', $giftCardData['Balance']);
                        $balance     = Mage::app()->getLocale()->getNumber($dataBalance);
                        if (($status == Magegiant_GiftCard_Model_Giftcard::STATUS_AVAILABLE) && !$balance) {
                            $status = Magegiant_GiftCard_Model_Giftcard::STATUS_USED;
                        }
                    } else {
                        continue;
                    }

                    $expired = null;
                    if (isset($giftCardData['Expiration At'])) {
                        $expired = $giftCardData['Expiration At'];
                        $expired = $expired ? date('Y-m-d', strtotime($expired)) : null;
                    }

                    $importData[] = array(
                        'code'       => $giftCardData['Code'],
                        'name'       => isset($giftCardData['Name']) ? $giftCardData['Name'] : '',
                        'amount'     => $balance,
                        'status'     => $status,
                        'active'     => $active,
                        'website_id' => $websiteId,
                        'created_at' => Varien_Date::now(true),
                        'expired_at' => $expired
                    );
                }

                $giftCardImported = $giftCardModel->setHistoryDetail($this->__('Imported by %s', Mage::getSingleton('admin/session')->getUser()->getUsername()))
                    ->createMultiple($importData);

                if ($size = $giftCardImported->getSize()) {
                    $this->_getSession()->addSuccess($this->__('%s Gift Card codes has been imported.', $size));
                } else {
                    $this->_getSession()->addSuccess($this->__('No Gift Card code was imported.'));
                }

                $this->_redirect('*/*/imported', array(
                    '_current' => true
                ));

                return $this;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Invalid file upload attempt'));
        }

        $this->_redirect('*/*/import', array(
            '_current' => true
        ));
    }

    public function importedAction()
    {
        $this->_getSession()->addNotice(
            $this->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_addContent(
            $this->getLayout()->createBlock('giftcard/adminhtml_import_close')
        );
        $this->renderLayout();
    }

    public function importSampleAction()
    {
        $filename = Mage::getBaseDir('media') . DS . 'magegiant' . DS . 'giftcard' . DS . 'example.csv';
        $this->_prepareDownloadResponse('example.csv', file_get_contents($filename));
    }

    public function getValueFromLabel($key, $giftCardData, $valueArrays)
    {
        if (isset($giftCardData[$key])) {
            $value = $giftCardData[$key];
        }

        if (!is_numeric($value) && array_key_exists($value, $valueArrays)) {
            return $valueArrays[$value];
        } else if (is_numeric($value) && in_array($value, $valueArrays)) {
            return $value;
        }

        return false;
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'giftcard.csv';
        $content  = $this->getLayout()
            ->createBlock('giftcard/adminhtml_giftcard_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'giftcard.xml';
        $content  = $this->getLayout()
            ->createBlock('giftcard/adminhtml_giftcard_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _construct()
    {
        $this->setUsedModuleName('Magegiant_GiftCard');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftcard/giftcard');
    }
}