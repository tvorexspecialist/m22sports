require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    $(document).ready(function() {
        var additionalContentBlock = $('.osc-addition-content-wrapper');
        var placeOrderBlock = $('.osc-place-order-wrapper');
        //wait until the last element (.payment-method) being rendered
        var existCondition = setInterval(function() {
            if ($('.osc-place-order-wrapper').length && $('.osc-addition-content-wrapper').length && $('.checkout-agreements-block').length) {
                clearInterval(existCondition);
                $('.osc-addition-content-wrapper').detach().insertAfter('.checkout-agreements-block');
            }
        }, 100);
    });
});
