define(
    [
        'underscore',
        'Rbofee_Extrafee/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Rbofee_Extrafee/js/model/fees',
        'Magento_Checkout/js/model/error-processor',
        'uiRegistry'
    ],
    function (_, resourceUrlManager, quote, storage, feesService, errorProcessor, registry) {
        "use strict";
        return function () {
            if (!feesService.rejectFeesLoading()) {
                var serviceUrl,
                    payload,
                    address,
                    paymentMethod,
                    requiredFields = ['countryId', 'region', 'regionId', 'postcode'],
                    newAddress = quote.shippingAddress() ? quote.shippingAddress() : quote.billingAddress(),
                    city;

                feesService.isLoading(true);
                serviceUrl = resourceUrlManager.getUrlForFetchFees(quote);
                address = _.pick(newAddress, requiredFields);
                paymentMethod = quote.paymentMethod() ? quote.paymentMethod().method : null;
                city = quote.shippingAddress() ? quote.shippingAddress().city : null;

                address.extension_attributes = {
                    advanced_conditions: {
                        custom_attributes: quote.shippingAddress() ? quote.shippingAddress().custom_attributes : [],
                        payment_method: paymentMethod,
                        city: city,
                        shipping_address_line: quote.shippingAddress() ? quote.shippingAddress().street : null,
                        billing_address_country: quote.billingAddress() ? quote.billingAddress().countryId : null
                    }
                };

                payload = {
                    addressInformation: {
                        address: address
                    }
                };

                if (quote.shippingMethod() && quote.shippingMethod()['method_code']) {
                    payload.addressInformation['shipping_method_code'] = quote.shippingMethod()['method_code'];
                    payload.addressInformation['shipping_carrier_code'] = quote.shippingMethod()['carrier_code'];
                }

                storage.post(
                    serviceUrl, JSON.stringify(payload), false
                ).done(
                    function (result) {
                        if (result['fees']) {
                            feesService.fees(result['fees']);
                        }

                        if (result['totals']) {
                            quote.setTotals(result['totals']);
                        }
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        feesService.isLoading(false);
                    }
                );
            }
        }
    }
);
