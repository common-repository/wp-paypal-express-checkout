<?php

/**
 *
 * @link              https://profiles.wordpress.org/palmoduledev/
 * @since             1.0.0
 * @package           Wp_Paypal_Express_Checkout
 *
 * @wordpress-plugin
 * Plugin Name:       PayPal Express Checkout for WordPress
 * Plugin URI:        https://profiles.wordpress.org/palmoduledev
 * Description:       PayPal Express Checkout for WordPress, Official PayPal Partner.
 * Version:           1.0.1
 * Author:            palmoduledev
 * Author URI:        https://profiles.wordpress.org/palmoduledev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-paypal-express-checkout
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_PAYPAL_EXPRESS_CHECKOUT_VERSION', '1.0.1' );
if (!defined('WP_PAYPAL_EXPRESS_CHECKOUT_ASSET_URL')) {
    define('WP_PAYPAL_EXPRESS_CHECKOUT_ASSET_URL', untrailingslashit(plugin_dir_url(__FILE__)));
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-paypal-express-checkout-activator.php
 */
function activate_wp_paypal_express_checkout() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-paypal-express-checkout-activator.php';
	Wp_Paypal_Express_Checkout_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-paypal-express-checkout-deactivator.php
 */
function deactivate_wp_paypal_express_checkout() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-paypal-express-checkout-deactivator.php';
	Wp_Paypal_Express_Checkout_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_paypal_express_checkout' );
register_deactivation_hook( __FILE__, 'deactivate_wp_paypal_express_checkout' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-paypal-express-checkout.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_paypal_express_checkout() {

	$plugin = new Wp_Paypal_Express_Checkout();
	$plugin->run();

}
run_wp_paypal_express_checkout();
