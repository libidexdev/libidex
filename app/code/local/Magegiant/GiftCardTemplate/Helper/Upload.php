<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageGiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @copyright   Copyright (c) 2014 MageGiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

/**
 * GiftCardTemplate Helper
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardTemplate_Helper_Upload extends Mage_Core_Helper_Abstract
{
    /**
     * upload image
     *
     * @param type $type
     * @return type
     */
    public function uploadFile($file, $path = null)
    {
        $filePath = self::getFileFolder($path);
        $fileName = "";
        if (isset($_FILES[$file]['name']) && $_FILES[$file]['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader($file);

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);

                $uploader->setFilesDispersion(false);
                $fileUploaded = $uploader->save($filePath, $_FILES[$file]['name']);
                $fileName     = $fileUploaded['file'];
            } catch (Exception $e) {
            }
        }

        return $fileName;
    }

    /**
     * create image folder
     *
     * @return null|string
     */
    public static function getFileFolder($path = null)
    {
        $filePath = Mage::getBaseDir('media') . DS . 'magegiant' . DS . 'giftcardtemplate' . DS . 'images' . DS . $path;
        if (!is_dir($filePath)) {
            try {
                mkdir($filePath);
                chmod($filePath, 0777);
            } catch (Exception $e) {
                return null;
            }
        }

        return $filePath;
    }

    /**
     * delete image file
     *
     * @param type $image
     * @return type
     */
    public function deleteFile($file, $path = null)
    {
        if (!$file) {
            return;
        }
        $fileDir = self::getFileFolder($path) . DS . $file;
        if (!file_exists($fileDir)) {
            return;
        }

        try {

            unlink($fileDir);
        } catch (Exception $e) {

        }
    }

    public function getBaseImageUrl($path = null)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'magegiant/giftcardtemplate/' . $path;
    }


}