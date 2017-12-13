<?php
class ObjectSource_DailyDeal_Block_Rss extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        $this->setCacheKey('rss_dailydeal_'.$this->getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $storeId       = $this->_getStoreId();
        $websiteId     = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroup = $this->_getCustomerGroupId();
        $now           = date('Y-m-d');
        $url           = Mage::getUrl('');
        $newUrl        = Mage::getUrl('dailydeal/index/rss');
        $lang          = Mage::getStoreConfig('general/locale/code');
        $title       = Mage::helper('rss')->__('%s - Daily Deal',Mage::app()->getStore($storeId)->getName());

        /** @var $rssObject Mage_Rss_Model_Rss */
        $rssObject = Mage::getModel('rss/rss');
        /** @var $collection Mage_SalesRule_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('catalogrule/rule')->getDailydealPromotionsCollection($storeId);

        $data = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
            'language'    => $lang,
        );
        $rssObject->_addHeader($data);

        $collection->setOrder('from_date','desc');
        $collection->load();

        foreach ($collection as $sr) {
            //var_dump($sr->getData());echo '<br><br>';
            // Find product related to the promotion
            $product = $sr->getDaildealPromotionProduct($sr);

            $categories = $product->getCategoryCollection()
                ->addAttributeToSelect('name');
            foreach ($categories as $category) {
                $categoryUrl = $category->getUrl();
            }

            // Build the email template
            $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('dailydeal_rss_content_template');
            $emailTemplateVariables = array();
            $emailTemplateVariables['product_image_large'] = $product->getImageUrl();
            $emailTemplateVariables['product_price_old'] = $product->getPrice();
            $emailTemplateVariables['product_price_new'] = 1;//Mage::getModel('catalogrule/rule')->calcProductPriceRule($product,$product->getPrice());
            $emailTemplateVariables['product_name'] = $product->getName();
            $emailTemplateVariables['product_description'] = $product->getDescription();
            $emailTemplateVariables['product_category'] = $categoryUrl;
            //print_r($emailTemplateVariables);
            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

            $description = '<table><tr>'.
            '<td style="text-decoration:none;">'.$sr->getDescription().
            '<br/>Discount Start Date: '.$this->formatDate($sr->getFromDate(), 'medium').
            ( $sr->getToDate() ? ('<br/>Discount End Date: '.$this->formatDate($sr->getToDate(), 'medium')):'').
            ($sr->getCouponCode() ? '<br/> Coupon Code: '.$sr->getCouponCode().'' : '').
            '</td>'.
            '</tr></table>';

             $data = array(
                 'title'       => $sr->getName(),
                 'description' => $description,
                 'link'        => $url,
                 'content'     => $processedTemplate
             );
            $rssObject->_addEntry($data);
            continue;
        }

        return $rssObject->createRssXml();
    }
}
