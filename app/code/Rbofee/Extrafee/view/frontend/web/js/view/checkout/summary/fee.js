define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'Magento_Customer/js/customer-data',
        'uiRegistry'
    ],
    function (Component, quote, priceUtils, totals, storage, registry) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Rbofee_Extrafee/checkout/summary/fee'
            },
            totals: quote.getTotals(),
            /**
             * Get formatted price
             * @returns {*|String}
             */
            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('rbofee_extrafee').value;
                }
                return this.getFormattedPrice(price);
            },
            /**
             * @returns {string}
             */
            getMethods: function(){
                var title = '',
                    rbofeeSegmentExtraFee = totals.getSegment('rbofee_extrafee');
                if (this.totals() && rbofeeSegmentExtraFee !== null &&  rbofeeSegmentExtraFee.value > 0) {
                    title = rbofeeSegmentExtraFee.title;
                }
                return title;
            },
            /**
             * @override
             */
            isDisplayed: function () {
                var rbofeeSegmentExtraFee = totals.getSegment('rbofee_extrafee'),
                    feeFieldSet = registry.get('checkout.sidebar.summary.block-rbofee-extrafee-summary.rbofee-extrafee-fieldsets')
                        ? registry.get('checkout.sidebar.summary.block-rbofee-extrafee-summary.rbofee-extrafee-fieldsets').elems()
                        : null,
                    isVisible = true;
                if (feeFieldSet) {
                    feeFieldSet.map(function (field) {
                        if (!field.visible()) {
                            isVisible = false;
                        }
                    })
                }

                if (this.totals()
                    && rbofeeSegmentExtraFee !== null
                    && rbofeeSegmentExtraFee.value > 0
                    && isVisible
                ) {
                    return true;
                }
                return false;
            }
        });
    }
);