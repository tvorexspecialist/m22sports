define(
    [
        'Rbofee_Extrafee/js/view/checkout/summary/fee',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
    ],
    function (Component, quote, totals) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Rbofee_Extrafee/checkout/cart/totals/fee'
            },
            totals: quote.getTotals(),
            /**
             * @returns {string}
             */
            getMethods: function(){
                var title = '';
                if (this.totals() &&  totals.getSegment('rbofee_extrafee').value > 0) {
                    title = totals.getSegment('rbofee_extrafee').title;
                }
                return title;
            }
        });
    }
);