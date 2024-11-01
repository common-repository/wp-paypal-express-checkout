<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Paypal_Express_Checkout
 * @subpackage Wp_Paypal_Express_Checkout/includes
 * @author     palmoduledev <palmoduledev@gmail.com>
 */
class Wp_Paypal_Express_Checkout_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                'wp-paypal-express-checkout', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}
