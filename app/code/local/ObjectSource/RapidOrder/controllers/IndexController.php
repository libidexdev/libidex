<?php
class ObjectSource_RapidOrder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function setRapidSelectionAction()
    {
        $rapidSelection = $this->getRequest()->getPost('rapidSelection');
        $websiteId = Mage::app()->getWebsite()->getId();
        if (!empty($rapidSelection)) {
            switch ($rapidSelection) {
                case 'STANDARD':
                    $code = '';
                    break;
                case 'SILVER':
                    $code = 'RAPID'.$rapidSelection.$websiteId;
                    break;
                default:
                    $err = true;
            }

            if (empty($err)) {
                $quote = Mage::getModel('checkout/cart')->getQuote();

                $quote->setCouponCode($code)
                    ->collectTotals()
                    ->save();
            }

        }

        $this->_redirect('checkout/cart');
    }
}