define([
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Rbofee_Extrafee/js/model/fees',
    ], function(
        $,
        ko,
        Component,
        feesService
    ) {
        'use strict';
        return Component.extend({
            isLoading: feesService.isLoading,
            defaults: {
                template: 'Rbofee_Extrafee/fee/block',
                modules: {
                    fieldset: '${ $.name }.rbofee-extrafee-fieldsets'
                }
            },
            getTemplate: function () {
                return this.template;
            },
            /**
             * @returns {*}
             */
            visible: function(){
                if (!this.fieldset()) {
                    return;
                }
                var elems = this.fieldset().elems.filter(function (el) {
                    return el.visible() === true;
                });

                return window.checkoutConfig.rbofee.extrafee.enabledOnCheckout == 1 &&
                    elems.length > 0;
            }
        });
    }
);
