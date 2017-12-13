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
 * GiftCardTemplate Observer Model
 *
 * @category    MageGiant
 * @package     MageGiant_GiftCardTemplate
 * @author      MageGiant Developer
 */
class Magegiant_GiftCardTemplate_Model_Observer
{
    /**
     *  Add GiftCard Template Html To Product
     *
     * @return Magegiant_GiftCardTemplate_Model_Observer
     */
    public function generateGiftCard($observer)
    {
        $event = $observer->getEvent();
        if (!$event)
            return $this;
        $container  = $event->getContainer();
        $buyRequest = $event->getRequest();
        $designData = array();
        if (isset($buyRequest['uploaded_html'])) {
            $designData['uploaded_html'] = $buyRequest['uploaded_html'];
        }
        if (isset($buyRequest['video_url'])) {
            $designData['video_url'] = $buyRequest['video_url'];
        }
        if (isset($buyRequest['selected_item'])) {
            $designData['selected_item'] = $buyRequest['selected_item'];
        }
        if (isset($buyRequest['message_box'])) {
            $designData['message_box'] = $buyRequest['message_box'];
        }
        if (isset($buyRequest['price_box'])) {
            $designData['price_box'] = $buyRequest['price_box'];
        }
        $container->addData($designData);

        return $this;
    }
}