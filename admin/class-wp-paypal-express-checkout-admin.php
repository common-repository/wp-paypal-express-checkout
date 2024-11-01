<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Paypal_Express_Checkout
 * @subpackage Wp_Paypal_Express_Checkout/admin
 * @author     palmoduledev <palmoduledev@gmail.com>
 */
class Wp_Paypal_Express_Checkout_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-paypal-express-checkout-admin.js', array('jquery'), $this->version, false);
    }

    public function wp_paypal_express_checkout_admin_page() {
        add_menu_page(
                __('PayPal Express Settings', 'textdomain'), __('PayPal Express', 'textdomain'), 'manage_options', 'wp-paypal-express-checkout-settings', array($this, 'wpdocs_unsub_page_callback'), WP_PAYPAL_EXPRESS_CHECKOUT_ASSET_URL . '/admin/images/wp-paypal-express-checkout-icon.png'
        );
    }

    public function wpdocs_unsub_page_callback() {
        $setting_tabs = apply_filters('wp_paypal_express_checkout_tab', array('general' => 'PayPal Express Checkout Setting'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs as $name => $label)
                echo '<a href="' . admin_url('admin.php?page=wp-paypal-express-checkout-settings&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
        </h2>
        <?php
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('wp_paypal_express_checkout_' . $setting_tabkey . '_setting_save_field');
                    do_action('wp_paypal_express_checkout_' . $setting_tabkey . '_setting');
                    break;
            }
        }
    }

    public function wp_paypal_express_checkout_general_setting_display() {
        $wp_paypal_express_checkout_general_setting = $this->wp_paypal_express_checkout_general_setting_field();
        $Html_output = new Wp_Paypal_Express_Checkout_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($wp_paypal_express_checkout_general_setting); ?>
            <p class="submit">
                <input type="submit" name="wp_paypal_express_checkout_general_setting_save" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public function wp_paypal_express_checkout_general_setting_save_field() {
        $wp_paypal_express_checkout_general_setting = $this->wp_paypal_express_checkout_general_setting_field();
        $Html_output = new Wp_Paypal_Express_Checkout_Html_output();
        $Html_output->save_fields($wp_paypal_express_checkout_general_setting);
    }

    public function wp_paypal_express_checkout_general_setting_field() {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        $page_array = array();
        foreach ($pages as $p) {
            $page_array[$p->ID] = $p->post_title;
        }
        $fields[] = array('title' => __('Getresponse Integration', 'woo-paypal-express-checkout'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Account Settings', 'woo-paypal-express-checkout'),
            'desc' => '',
            'id' => 'account_settings',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_enabled',
            'title' => __('Enable/Disable', 'woo-paypal-express-checkout'),
            'label' => __('Enable PayPal Express', 'woo-paypal-express-checkout'),
            'type' => 'checkbox',
            'desc' => '',
            'default' => 'no',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_sandbox',
            'title' => __('Sandbox Mode', 'woo-paypal-express-checkout'),
            'type' => 'checkbox',
            'label' => __('Enable PayPal Sandbox Mode', 'woo-paypal-express-checkout'),
            'default' => 'yes',
            'desc' => sprintf(__('Place the payment gateway in development mode. Sign up for a developer account <a href="%s" target="_blank">here</a>', 'woo-paypal-express-checkout'), 'https://developer.paypal.com/'),
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_rest_client_id_sandbox',
            'title' => __('Sandbox Client ID', 'woo-paypal-express-checkout'),
            'type' => 'password',
            'desc' => 'Enter your Sandbox PayPal Rest API Client ID',
            'css' => 'min-width:654px;',
            'default' => ''
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_rest_secret_id_sandbox',
            'title' => __('Sandbox Secret ID', 'woo-paypal-express-checkout'),
            'type' => 'password',
            'desc' => __('Enter your Sandbox PayPal Rest API Secret ID.', 'woo-paypal-express-checkout'),
            'default' => 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R',
            'css' => 'min-width:654px;',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_rest_client_id_live',
            'title' => __('Live Client ID', 'woo-paypal-express-checkout'),
            'type' => 'password',
            'desc' => 'Enter your PayPal Rest API Client ID',
            'default' => '',
            'css' => 'min-width:654px;',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_rest_secret_id_live',
            'title' => __('Live Secret ID', 'woo-paypal-express-checkout'),
            'type' => 'password',
            'desc' => __('Enter your PayPal Rest API Secret ID.', 'woo-paypal-express-checkout'),
            'default' => '',
            'css' => 'min-width:654px;',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_enabled_cc',
            'title' => __('Enable PayPal Credit', 'woo-paypal-express-checkout'),
            'label' => __('Enable PayPal Credit', 'woo-paypal-express-checkout'),
            'type' => 'checkbox',
            'desc' => 'If enable this option, it will display PayPal Credit Button below PayPal Express checkout button.',
            'default' => 'no',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_landing_page',
            'title' => __('Landing Page', 'woo-paypal-express-checkout'),
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'description' => __('Type of PayPal page to display.', 'woo-paypal-express-checkout'),
            'default' => 'Login',
            'desc_tip' => true,
            'options' => array(
                'Billing' => _x('Billing (Non-PayPal account)', 'Type of PayPal page', 'woo-paypal-express-checkout'),
                'Login' => _x('Login (PayPal account login)', 'Type of PayPal page', 'woo-paypal-express-checkout'),
            ),
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_cancel_page',
            'title' => __('Cancel Page', 'paypal-for-woocommerce'),
            'desc' => __('Sets the page users will be returned to if they click the Cancel link on the PayPal checkout pages.'),
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'options' => $page_array,
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_thank_you_page',
            'title' => __('Thank You Page', 'paypal-for-woocommerce'),
            'desc' => __('Sets the page users will be returned to this page, after completion of payment.'),
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'options' => $page_array,
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_button_styles',
            'title' => __('Express Checkout Custom Button Styles', 'woo-paypal-express-checkout'),
            'desc' => 'Customize your PayPal button with colors, sizes and shapes.',
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_button_size',
            'title' => __('Button Size', 'woo-paypal-express-checkout'),
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'desc' => __('Type of PayPal Button Size (small | medium | responsive).', 'woo-paypal-express-checkout'),
            'default' => 'small',
            'options' => array(
                'small' => __('Small', 'woo-paypal-express-checkout'),
                'medium' => __('Medium', 'woo-paypal-express-checkout'),
                'responsive' => __('Responsive', 'woo-paypal-express-checkout'),
            ),
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_button_shape',
            'title' => __('Button Shape', 'woo-paypal-express-checkout'),
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'desc' => __('Type of PayPal Button Shape (pill | rect).', 'woo-paypal-express-checkout'),
            'default' => 'pill',
            'options' => array(
                'pill' => __('Pill', 'woo-paypal-express-checkout'),
                'rect' => __('Rect', 'woo-paypal-express-checkout')
            ),
        );
        $fields[] = array(
            'id' => 'woo_paypal_express_checkout_button_color',
            'title' => __('Button Color', 'woo-paypal-express-checkout'),
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'desc' => __('Type of PayPal Button Color (gold | blue | silver).', 'woo-paypal-express-checkout'),
            'default' => 'gold',
            'options' => array(
                'gold' => __('Gold', 'woo-paypal-express-checkout'),
                'blue' => __('Blue', 'woo-paypal-express-checkout'),
                'silver' => __('Silver', 'woo-paypal-express-checkout')
            ),
        );
        
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }
    
    public function wp_paypal_express_checkout_register_post_types() {
        global $wpdb;
        if (post_type_exists('pal_paypal_payment')) {
            return;
        }
        do_action('wp_paypal_express_checkout_register_post_types');

        register_post_type('pal_paypal_payment', apply_filters('wp_paypal_express_checkout_register_post_types_pal_paypal_payment', array(
            'labels' => array(
                'name' => __('PayPal Payment', 'woo-paypal-express-checkout'),
                'singular_name' => __('PayPal Payment', 'woo-paypal-express-checkout'),
                'menu_name' => _x('PayPal Payment', 'Admin menu name', 'woo-paypal-express-checkout'),
                'add_new' => __('Add PayPal Payment', 'woo-paypal-express-checkout'),
                'add_new_item' => __('Add New PayPal Payment', 'woo-paypal-express-checkout'),
                'edit' => __('Edit', 'woo-paypal-express-checkout'),
                'edit_item' => __('View PayPal Payment', 'woo-paypal-express-checkout'),
                'new_item' => __('New PayPal Payment', 'woo-paypal-express-checkout'),
                'view' => __('View PayPal Payment', 'woo-paypal-express-checkout'),
                'view_item' => __('View PayPal Payment', 'woo-paypal-express-checkout'),
                'search_items' => __('Search PayPal Payment', 'woo-paypal-express-checkout'),
                'not_found' => __('No PayPal Payment found', 'woo-paypal-express-checkout'),
                'not_found_in_trash' => __('No PayPal Payment found in trash', 'woo-paypal-express-checkout'),
                'parent' => __('Parent PayPal Payment', 'woo-paypal-express-checkout')
            ),
            'description' => __('This is where you can add new payment to your store.', 'woo-paypal-express-checkout'),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false, 
            ),
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'pal_paypal_payment'),
            'query_var' => true,
            'menu_icon' => WP_PAYPAL_EXPRESS_CHECKOUT_ASSET_URL . '/admin/images/wp-paypal-express-checkout-icon.png',
            'supports' => array('custom-fields'),
            'has_archive' => true,
            'show_in_nav_menus' => true
                        )
                )
        );
    }
    
    public function pal_paypal_payment_for_wordpress_add_pal_paypal_payment_columns() {
        $columns = array();
        $columns['cb'] = '<input type="checkbox" />';
        $columns['title'] = _x('Transaction ID', 'paypal-ipn');
        $columns['payment_date'] = _x('Date', 'paypal-ipn');
        $columns['first_name'] = _x('Name / Company', 'paypal-ipn');
        $columns['mc_gross'] = __('Amount', 'paypal-ipn');
        $columns['txn_type'] = __('Transaction Type', 'paypal-ipn');
        $columns['payment_status'] = __('Payment Status', 'paypal-ipn');
        $columns['email'] = __('Email', 'paypal-ipn');
        return $columns;
    }
    
    public function pal_paypal_payment_for_wordpress_render_pal_paypal_payment_columns($column) {
         global $post;

        switch ($column) {
           
            case 'payment_date' :
                echo get_the_date();
                break;
            case 'first_name' :
                echo esc_attr(get_post_meta($post->ID, 'first_name', true) . ' ' . get_post_meta($post->ID, 'last_name', true));
                echo (get_post_meta($post->ID, 'payer_business_name', true)) ? '<br />' . get_post_meta($post->ID, 'payer_business_name', true) : '';
                break;
            case 'mc_gross' :
                $mc_gross = get_post_meta($post->ID, 'total', true);
                if (isset($mc_gross) && !empty($mc_gross)) {
                    echo esc_attr($mc_gross);
                }
                break;
            case 'txn_type' :
                $txn_type = get_post_meta($post->ID, 'transactionType', true);
                if (isset($txn_type) && !empty($txn_type)) {
                    echo esc_attr($txn_type);
                } 
                break;

            case 'payment_status' :
                echo esc_attr(get_post_meta($post->ID, 'paymentState', true));
                break;
            case 'email' :
                echo esc_attr(get_post_meta($post->ID, 'email', true));
        }
    }

}
