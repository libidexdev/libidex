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
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */

function changeUseCredit(el, url, contenEl, amountEl) {
    amountEl = (typeof amountEl !== 'undefined') ? amountEl : 'credit_amount';
    contenEl = (typeof contenEl !== 'undefined') ? contenEl : '.payment-form-giftcard-credit';

    expandDetails(el, contenEl);

    if($(amountEl).value){
        checkGiftcard(url, amountEl, 'check-gc-credit-status', 'check-gc-credit-please-wait', {use_credit: el.checked ? 1 : 0});
    }
}