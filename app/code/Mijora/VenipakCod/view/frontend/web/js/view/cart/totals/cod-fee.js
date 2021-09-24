
define(
    [
        'ko',
        'Mijora_VenipakCod/js/view/checkout/summary/cod-fee',
        'Mijora_VenipakCod/js/view/payment/cod-payments'
    ],
    function (ko, Component, cashondelivery) {
        'use strict';

        return Component.extend({
            isDisplayed: function () {
                return this.isFullMode();
            }
        });
    }
);