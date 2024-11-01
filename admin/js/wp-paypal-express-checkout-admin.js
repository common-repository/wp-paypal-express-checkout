(function ($) {
    'use strict';
    $(window).load(function () {
        $('#woo_paypal_express_checkout_sandbox').change(function () {
            var sandbox = jQuery('#woo_paypal_express_checkout_rest_client_id_sandbox, #woo_paypal_express_checkout_rest_secret_id_sandbox').closest('tr');
            var production = jQuery('#woo_paypal_express_checkout_rest_client_id_live, #woo_paypal_express_checkout_rest_secret_id_live').closest('tr');
            if ($(this).is(':checked')) {
                sandbox.show();
                production.hide();
            } else {
                sandbox.hide();
                production.show();
            }
        }).change();
    });
})(jQuery);