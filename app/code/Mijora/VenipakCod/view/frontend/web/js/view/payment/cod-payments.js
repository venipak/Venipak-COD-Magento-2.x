define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'venipak_cod',
                component: 'Mijora_VenipakCod/js/view/payment/method-renderer/cod-method'
            }
        );
        return Component.extend({});
    }
);