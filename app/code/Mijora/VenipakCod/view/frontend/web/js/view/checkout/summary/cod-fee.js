define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'knockout',
        'Magento_Checkout/js/action/get-totals',
        'mage/url',
        'jquery',
        'mage/translate'
    ],
    function (Component, quote, priceUtils, totals, ko, getTotalsAction, url, $, t) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Mijora_VenipakCod/checkout/cod-fee',
                VenipakCodTitle: $.mage.__('Cash On Delivery Fee'),
                shouldDisplay: ko.observable(false),
                value: ko.observable(0)
            },
            initialize: function() {
                this._super();
                
                quote.paymentMethod.subscribe((function(newPaymentMethod) {

                    var linkUrl = url.build('VenipakCod/checkout/applyPaymentMethod');
                    jQuery.ajax(linkUrl, {
                        data: {payment_method: newPaymentMethod},
                        complete: function () {
                            getTotalsAction([]);
                        }
                    });
        
                    getTotalsAction([]);
                }).bind(this));
                
                quote.totals.subscribe((function (newTotals) {
                    this.value(this.getFormattedFeeValue(newTotals));
                    if (this.getFeeValue(newTotals) !== 0.0){
                        this.shouldDisplay(true);
                    } else {
                        this.shouldDisplay(false);
                    }
                }).bind(this));
                
            },
            
            getFeeValue: function(totals) {
                if (typeof totals.total_segments === 'undefined' || !totals.total_segments instanceof Array) {
                    return 0.0;
                }

                return totals.total_segments.reduce(function (cashOnDeliveryTotalValue, currentTotal) {
                    return currentTotal.code === 'venipak_cod_fee' ? currentTotal.value : cashOnDeliveryTotalValue
                }, 0.0);
            },
            getFormattedFeeValue: function(totals) {
                return this.getFormattedPrice(this.getFeeValue(totals));
            }
        });
    }
);