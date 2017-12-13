<?php
$installer = $this;

$installer->startSetup();

/**
 * Commented out to make sure that the rapid service will be enabled with the new settings after deployment to live.
 */
/*
$ruleCoupon = Mage::getResourceModel('salesrule/coupon_collection')
    ->addFieldToFilter('code', 'RAPIDSILVER1')
    ->getFirstItem();

if ($ruleCoupon->getRuleId()) {
    $rule = Mage::getModel('salesrule/rule')->load($ruleCoupon->getRuleId());
    $rule->setDiscountAmount(0)->save();
}
*/

$installer->endSetup();