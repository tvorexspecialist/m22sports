define([
    'jquery',
    'Magento_Sales/order/create/scripts'
], function ($, adminOrder) {
    return {
        initFeeOrderCreate: function() {
            //update Fees after a shipping method set
            window.AdminOrder.prototype.setShippingMethod = function(method) {
                var data = {};
                data['order[shipping_method]'] = method;
                this.loadArea(['shipping_method', 'items', 'totals', 'billing_method'], true, data); //add 'items'
            };

            //update Fees after a payment method switch
            window.payment.switchMethod= function(method) {
                $('#edit_form')
                    .off('submitOrder')
                    .on('submitOrder', function(){
                        jQuery(this).trigger('realOrder');
                    });
                $('#edit_form').trigger('changePaymentMethod', [method]);
                this.setPaymentMethod(method);
                var data = {};
                data['order[payment_method]'] = method;
                this.loadArea(['card_validation', 'items'], true, data);  //add 'items'
            }.bind(window.order);

            return this;
        },

        updateExtraFee: function() {
            var fees = {};
            $('.rbofee-extrafee-fees').each( function() {
               fees[$(this).attr('fee-id')] = [];
            });

            var extraFeeDiv = 'form#edit_form .rbofee_extrafee ';
            $(extraFeeDiv + ':input:checked,' + extraFeeDiv + 'select option:selected').each(function() {
                if ($(this).val()) {
                    var feeId = $(this).closest('.rbofee-extrafee-fees').attr('fee-id');
                    fees[feeId].push(Number($(this).val()));
                }
            });
            if (!$.isEmptyObject(fees)) {
                var data = {};
                data.am_extra_fees = JSON.stringify(fees);
                order.loadArea(
                    ['items', 'shipping_method', 'totals', 'billing_method'],
                    true,
                    data
                );
            }
        },
    }
});
