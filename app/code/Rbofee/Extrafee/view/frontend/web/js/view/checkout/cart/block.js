define(
    [
        'Rbofee_Extrafee/js/view/checkout/summary/block'
    ],
    function (Component, quote, totals) {
        'use strict';

        return Component.extend({
            /**
             * @returns {*}
             */
            visible: function(){
                if (!this.fieldset()) {
                    return;
                }
                var elems = this.fieldset().elems.filter(function(el){
                    return el.visible() === true;
                });

                return window.checkoutConfig.rbofee.extrafee.enabledOnCart == 1 &&
                    elems.length > 0;
            }
        });
    }
);