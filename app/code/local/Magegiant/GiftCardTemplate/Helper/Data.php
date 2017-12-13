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
class Magegiant_GiftCardTemplate_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getVideoThumbFromUrl($url, $type = 'hqdefault')
    {
        $videoId = $this->getVideoIdByUrl($url);
        $thumb   = $this->getVideoThumbByType($videoId, $type);

        return $thumb;
    }

    public function getVideoIdByUrl($url)
    {
        $videoId     = '';
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $params);
        if (isset($params['v'])) {
            $videoId = $params['v'];
        }

        return $videoId;
    }

    public function getVideoThumbByType($id, $type)
    {
        $thumb = '';
        if ($id) {
            $isSecure = Mage::app()->getStore()->isCurrentlySecure();
            if ($isSecure) {
                $thumb = 'https://img.youtube.com/vi/' . $id . '/' . $type . '.jpg';
            } else {
                $thumb = 'http://img.youtube.com/vi/' . $id . '/' . $type . '.jpg';
            }

        }

        return $thumb;
    }

    /**
     * Get media folder by path
     *
     * @param null $path
     * @return string
     */
    public function getMediaUrl($path = null)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $path;

    }

    public function addSelectAll($id)
    {
        return
            '<span>
                    <a id="select_' . $id . '" style="cursor:pointer" onclick="$$(\'#' . $id . ' option\').each(function(el){el.selected=true});this.hide();$(\'unselect_' . $id . '\').show()">' . $this->__('Select All') . '</a>
                <a id="unselect_' . $id . '" style="cursor:pointer;display:none;" onclick="$$(\'#' . $id . ' option\').each(function(el){el.selected=false});this.hide();$(\'select_' . $id . '\').show()">' . $this->__('Unselected') . '</a>
        </span>';
    }

}