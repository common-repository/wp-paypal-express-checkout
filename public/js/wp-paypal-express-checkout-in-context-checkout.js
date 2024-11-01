;
(function ($, window, document) {
    paypal.Button.render({
        env: wp_paypal_express_checkout_param.env, // Or 'sandbox'
        locale: wp_paypal_express_checkout_param.locale,
        style: {
            label: 'checkout',
            size: wp_paypal_express_checkout_param.size,
            shape: wp_paypal_express_checkout_param.shape,
            color: wp_paypal_express_checkout_param.color
        },
        commit: true,
        client: {
            sandbox: wp_paypal_express_checkout_param.client_id,
            production: wp_paypal_express_checkout_param.client_id
        },
        payment: function (data, actions) {
            return actions.payment.create({
                meta: {
                    partner_attribution_id: 'Palmodule_SP'
                },
                payment: {
                    transactions: [
                        {
                            amount: {total: wp_paypal_express_checkout_param.price, currency: 'USD'}
                        }
                    ],
                    redirect_urls: {return_url: wp_paypal_express_checkout_param.return_url, cancel_url: wp_paypal_express_checkout_param.cancel_url}
                }
            });
        },
        onAuthorize: function (data, actions) {
            actions.payment.execute().then(function (result) {
                $json_response = result;
                var paymentState = $json_response['state'];
                var finalAmount = $json_response['transactions'][0]['amount']['total'];
                var currency = $json_response['transactions'][0]['amount']['currency'];
                var transactionID = $json_response['transactions'][0]['related_resources'][0]['sale']['id'];
                var payerFirstName = $json_response['payer']['payer_info']['first_name'];
                var last_name = $json_response['payer']['payer_info']['last_name'];
                var email = $json_response['payer']['payer_info']['email'];
                var recipient_name = $json_response['payer']['payer_info']['shipping_address']['recipient_name'], FILTER_SANITIZE_SPECIAL_CHARS;
                var addressLine1 = $json_response['payer']['payer_info']['shipping_address']['line1'];
                var addressLine2 = $json_response['payer']['payer_info']['shipping_address']['line2'];
                var city = $json_response['payer']['payer_info']['shipping_address']['city'];
                var state = $json_response['payer']['payer_info']['shipping_address']['state'];
                var postalCode = $json_response['payer']['payer_info']['shipping_address']['postal_code'];
                var transactionType = $json_response['intent'];
                data.returnUrl = data.returnUrl + '&txn_id=' + transactionID + '&paymentState=' + paymentState + '&total=' + finalAmount + '&currency=' + currency + '&first_name=' + payerFirstName + '&last_name=' + last_name + '&recipient_name=' + recipient_name + '&addressLine1=' + addressLine1 + '&addressLine2=' + addressLine2 + '&city=' + city + '&state=' + state + '&postalCode=' + postalCode + '&postalCode=' + postalCode + '&transactionType=' + transactionType + '&email=' + email;
                actions.redirect();
            });
        },
        onCancel: function (data, actions) {
            return actions.redirect();
        }
    }, '#paypal-button-container');


    if (wp_paypal_express_checkout_param.enable_cc == 'yes') {
        paypal.Button.render({
            env: wp_paypal_express_checkout_param.env, // Or 'sandbox'
            locale: wp_paypal_express_checkout_param.locale,
            style: {
                label: 'credit',
                size: wp_paypal_express_checkout_param.SIZE,
                shape: wp_paypal_express_checkout_param.SHAPE
            },
            commit: true,
            client: {
                sandbox: wp_paypal_express_checkout_param.client_id,
                production: wp_paypal_express_checkout_param.client_id
            },
            payment: function (data, actions) {
                return actions.payment.create({
                    meta: {
                        partner_attribution_id: 'Palmodule_SP'
                    },
                    payment: {
                        transactions: [
                            {
                                amount: {total: wp_paypal_express_checkout_param.price, currency: 'USD'}
                            }
                        ],
                        redirect_urls: {return_url: wp_paypal_express_checkout_param.return_url, cancel_url: wp_paypal_express_checkout_param.cancel_url},
                        payer: {
                            payment_method: 'paypal',
                            external_selected_funding_instrument_type: 'CREDIT'
                        }
                    }
                });
            },
            onAuthorize: function (data, actions) {
                actions.payment.execute().then(function (result) {
                    $json_response = result;
                    var paymentState = $json_response['state'];
                    var finalAmount = $json_response['transactions'][0]['amount']['total'];
                    var currency = $json_response['transactions'][0]['amount']['currency'];
                    var transactionID = $json_response['transactions'][0]['related_resources'][0]['sale']['id'];
                    var payerFirstName = $json_response['payer']['payer_info']['first_name'];
                    var last_name = $json_response['payer']['payer_info']['last_name'];
                    var email = $json_response['payer']['payer_info']['email'];
                    var recipient_name = $json_response['payer']['payer_info']['shipping_address']['recipient_name'], FILTER_SANITIZE_SPECIAL_CHARS;
                    var addressLine1 = $json_response['payer']['payer_info']['shipping_address']['line1'];
                    var addressLine2 = $json_response['payer']['payer_info']['shipping_address']['line2'];
                    var city = $json_response['payer']['payer_info']['shipping_address']['city'];
                    var state = $json_response['payer']['payer_info']['shipping_address']['state'];
                    var postalCode = $json_response['payer']['payer_info']['shipping_address']['postal_code'];
                    var transactionType = $json_response['intent'];
                    data.returnUrl = data.returnUrl + '&txn_id=' + transactionID + '&paymentState=' + paymentState + '&total=' + finalAmount + '&currency=' + currency + '&first_name=' + payerFirstName + '&last_name=' + last_name + '&recipient_name=' + recipient_name + '&addressLine1=' + addressLine1 + '&addressLine2=' + addressLine2 + '&city=' + city + '&state=' + state + '&postalCode=' + postalCode + '&postalCode=' + postalCode + '&transactionType=' + transactionType + '&email=' + email;
                    actions.redirect();
                });
            },
            onCancel: function (data, actions) {
                return actions.redirect();
            }
        }, '#paypal-button-container-cc');

    }

})(jQuery, window, document);