define([
        'ko',
        'jquery',
        'Magento_Ui/js/form/element/abstract',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Rbofee_Extrafee/js/action/select-fee',
    ], function(
        ko,
        $,
        AbstractField,
        priceUtils,
        quote,
        selectFeeAction
    ) {
        'use strict';

        return AbstractField.extend({
            defaults: {
                template: 'Rbofee_Extrafee/fee/item',
                templates: {
                    radio: 'Rbofee_Extrafee/fee/item/radio',
                    checkbox: 'Rbofee_Extrafee/fee/item/checkbox',
                    dropdown: 'Rbofee_Extrafee/fee/item/dropdown'
                },
                frontendType: 'dropdown',
                feeId: null,
                options: [],
                currentValue: []
            },
            /**
             * @returns {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe([
                        'currentValue',
                        'options'
                    ]);

                return this;
            },
            /**
             * @returns {exports.initialize}
             */
            initialize: function(){
                this._super();
                this.currentValue.subscribe(this.setFee.bind(this));
                return this;
            },
            /**
             * apply fee to totals
             * @param optionId
             */
            setFee: function(optionId){
                var optionsIds = typeof(optionId) === 'object' ? optionId : [optionId];
                selectFeeAction(this.feeId, optionsIds);
            },
            /**
             *
             * @param config
             * @returns {exports.initConfig}
             */
            initConfig: function (config) {
                this._super();

                if (this.frontendType === 'dropdown') {
                    this.elementTmpl = this.templates.dropdown;
                } else if (this.frontendType === 'checkbox') {
                    this.elementTmpl = this.templates.checkbox;
                } else if (this.frontendType === 'radio') {
                    this.elementTmpl = this.templates.radio;
                }
                return this;
            },
            /**
             *
             * @returns {Array}
             */
            getOptions: function() {
                //Magento 2.1.9 <= value type
                if ($.type(this.options()[0]) !== undefined &&  $.type(this.options()[0] === 'string')) {
                    var self = this;

                    $.map(this.options(), function( val, i ) {
                        if ($.type(val) === 'string') {
                            self.options()[i] = JSON.parse(val);
                        }
                    });
                }

                return this.options;
            },
            optionsText: function(item) {
                return item.label + ' ' + this.getFormattedPrice(item.price);
            },
            /**
             *
             * @param item
             * @returns {*}
             */
            optionsValue: function(item) {
                return item.index + '';
            },
            /**
             * Format shipping price.
             * @returns {String}
             */
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            }
        });
    }
);
