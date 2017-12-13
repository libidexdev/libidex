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

function checkGiftcard(url, giftCodeElm, statusElm, pleaseWaitElm, parameters) {
    giftCodeElm = (typeof giftCodeElm !== 'undefined') ? giftCodeElm : 'gift_code';
    statusElm = (typeof statusElm !== 'undefined') ? statusElm : 'check-gc-status';
    pleaseWaitElm = (typeof pleaseWaitElm !== 'undefined') ? pleaseWaitElm : 'check-gc-please-wait';
    parameters = (typeof parameters !== 'undefined') ? parameters : {};

    if (!validateGiftCardCode(giftCodeElm)) {
        return;
    }

    var gc_status_element = $(statusElm);
    $(pleaseWaitElm).show();
    gc_status_element.hide();

    parameters[giftCodeElm] = $(giftCodeElm).value;

    url = getGiftCardUpdateUrl(url);
    new Ajax.Request(url, {
        method: 'post',
        parameters: parameters,
        onSuccess: function (xhr) {
            var response = xhr.responseText.evalJSON();
            if (response.update_payment) {
                reloadPayment(true);
            } else {
                gc_status_element.innerHTML = response.html;

                $(pleaseWaitElm).hide();
                gc_status_element.show();
            }
        },
        onException: function () {
            window.location.reload();
        }
    });
}

function validateGiftCardCode(giftCodeElm) {
    var gift_code = $(giftCodeElm);
    gift_code.addClassName('required-entry');
    if (!Validation.validate(gift_code)) {
        gift_code.removeClassName('required-entry');
        return false;
    }
    gift_code.removeClassName('required-entry');

    return true;
}

function viewCode(element, showCode, hiddenCode) {
    if (element.hasClassName('code-view')) {
        element.removeClassName('code-view');
        element.innerHTML = hiddenCode;
    } else {
        element.addClassName('code-view');
        element.innerHTML = showCode;
    }

    return;
}

function getGiftCardUpdateUrl(url) {
    if (window.location.href.match('https://') && !url.match('https://')) {
        url = url.replace('http://', 'https://');
    }
    if (!window.location.href.match('https://') && url.match('https://')) {
        url = url.replace('https://', 'http://');
    }

    return url;
}

function reloadPayment(payment_update) {
    if ($$('body').first().hasClassName('checkout-onepage-index') && typeof shippingMethod != 'undefined' && typeof shippingMethod.save == 'function') {
        shippingMethod.save();
    } else if ($$('body').first().hasClassName('onestepcheckout-index-index') && typeof save_shipping_method == 'function') {
        save_shipping_method(shipping_method_url, payment_update, true);
    } else if (typeof updateShippingMethod == 'function') {
        updateShippingMethod();
    } else if ($$('body').first().hasClassName('onepagecheckout-index-index') && typeof checkout != 'undefined' && typeof checkout.update == 'function') {
        checkout.update({
            'payment-method': payment_update,
            'shipping-method': 0,
            'review': 1
        });
        /**
         * Deivison Arthur Onepage checkout - Magento Brazil free
         */
    } else if ($$('body').first().hasClassName('gomage-checkout-onepage-index') && typeof checkout != 'undefined' && typeof checkout.submit == 'function') {
        checkout.submit({}, 'get_totals');
        /**
         * Gomage light checkout
         */
    } else if ($$('body').first().hasClassName('opc-index-index') && typeof IWD.OPC.savePayment == 'function') {
        if (!payment_update) IWD.OPC.savePayment();
        else IWD.OPC.Shipping.save();
        /**
         * IWD onepage checkout
         */
    } else if ($$('body').first().hasClassName('firecheckout-index-index') && typeof checkout != 'undefined' && typeof checkout.update == 'function') {
        checkout.update(checkout.urls.paymentdata, {
            'review': 1,
            'payment-method': payment_update
        });
        /**
         * TM_FireCheckout
         */
    } else {
        window.location.reload();
    }
}