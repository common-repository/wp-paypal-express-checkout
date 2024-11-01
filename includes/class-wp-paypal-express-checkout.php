<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Paypal_Express_Checkout
 * @subpackage Wp_Paypal_Express_Checkout/includes
 * @author     palmoduledev <palmoduledev@gmail.com>
 */
class Wp_Paypal_Express_Checkout {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wp_Paypal_Express_Checkout_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('WP_PAYPAL_EXPRESS_CHECKOUT_VERSION')) {
            $this->version = WP_PAYPAL_EXPRESS_CHECKOUT_VERSION;
        } else {
            $this->version = '1.0.1';
        }
        $this->plugin_name = 'wp-paypal-express-checkout';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wp_Paypal_Express_Checkout_Loader. Orchestrates the hooks of the plugin.
     * - Wp_Paypal_Express_Checkout_i18n. Defines internationalization functionality.
     * - Wp_Paypal_Express_Checkout_Admin. Defines all hooks for the admin area.
     * - Wp_Paypal_Express_Checkout_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-paypal-express-checkout-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-paypal-express-checkout-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-paypal-express-checkout-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-paypal-express-checkout-html-output.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-paypal-express-checkout-public.php';

        $this->loader = new Wp_Paypal_Express_Checkout_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wp_Paypal_Express_Checkout_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Wp_Paypal_Express_Checkout_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Wp_Paypal_Express_Checkout_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'wp_paypal_express_checkout_admin_page');
        $this->loader->add_action('wp_paypal_express_checkout_general_setting', $plugin_admin, 'wp_paypal_express_checkout_general_setting_display');
        $this->loader->add_action('wp_paypal_express_checkout_general_setting_save_field', $plugin_admin, 'wp_paypal_express_checkout_general_setting_save_field');
        $this->loader->add_action('init', $plugin_admin, 'wp_paypal_express_checkout_register_post_types', 5);
        $this->loader->add_action('manage_edit-pal_paypal_payment_columns', $plugin_admin, 'pal_paypal_payment_for_wordpress_add_pal_paypal_payment_columns', 10, 2);
        $this->loader->add_action('manage_pal_paypal_payment_posts_custom_column', $plugin_admin, 'pal_paypal_payment_for_wordpress_render_pal_paypal_payment_columns', 2);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Wp_Paypal_Express_Checkout_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_filter('the_content', $plugin_public, 'wp_paypal_express_checkout_thankyou', 999, 1);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wp_Paypal_Express_Checkout_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
