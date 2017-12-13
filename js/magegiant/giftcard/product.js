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

if (typeof GiftCard == 'undefined') {
    var GiftCard = {};
}

GiftCard.OptionsPrice = Class.create();
GiftCard.OptionsPrice.prototype = {
    initialize: function (config, amountJson) {
        this.productId = config.productId;
        this.priceFormat = config.priceFormat;
        this.includeTax = config.includeTax;
        this.defaultTax = config.defaultTax;
        this.currentTax = config.currentTax;
        this.showIncludeTax = config.showIncludeTax;
        this.showBothPrices = config.showBothPrices;

        this.amountCg = amountJson.amount;
        this.priceCg = amountJson.price;
        this.typeCg = amountJson.type;

        this.amountElement = $('giftcard_amount');

        this.containers = {};

        this.displayZeroPrice = true;

        this.initPrices();
    },

    initPrices: function () {
        this.containers[0] = 'product-price-' + this.productId;
        this.containers[2] = 'price-including-tax-' + this.productId;
        this.containers[3] = 'price-excluding-tax-' + this.productId;
    },

    loadPrice: function () {
        var price = this.getPriceByAmount(this.amountElement.value);
        var formattedPrice;

        if (this.includeTax == 'true') {
            var tax = price / (100 + this.defaultTax) * this.defaultTax;
            var excl = price - tax;
            var incl = excl * (1 + (this.currentTax / 100));
        } else {
            var tax = price * (this.currentTax / 100);
            var excl = price;
            var incl = excl + tax;
        }

        $H(this.containers).each(function (pair) {
            if ($(pair.value)) {
                if (pair.value == 'price-including-tax-' + this.productId) {
                    price = incl;
                } else if (pair.value == 'price-excluding-tax-' + this.productId) {
                    price = excl;
                } else {
                    if (this.showIncludeTax) {
                        price = incl;
                    } else {
                        price = excl;
                    }
                }

                if (price < 0) price = 0;

                if (price > 0 || this.displayZeroPrice) {
                    formattedPrice = this.formatPrice(price);
                } else {
                    formattedPrice = '';
                }

                if ($(pair.value).select('.price')[0]) {
                    $(pair.value).select('.price')[0].innerHTML = formattedPrice;
                } else {
                    $(pair.value).innerHTML = formattedPrice;
                }
            }
            ;
        }.bind(this));
        return formattedPrice;
    },

    getPriceByAmount: function (amount) {
        var priceAmount = 0;
        if (this.typeCg == 'range') {
            priceAmount = amount * this.priceCg / 100;
        } else {
            for (var i in this.amountCg) {
                if (this.amountCg[i] == amount) {
                    priceAmount = this.priceCg[i];
                }
            }
        }

        return parseFloat(priceAmount);
    },

    formatPrice: function (price) {
        return formatCurrency(price, this.priceFormat);
    }
}
