<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Paypal_Express_Checkout
 * @subpackage Wp_Paypal_Express_Checkout/public
 * @author     palmoduledev <palmoduledev@gmail.com>
 */
class Wp_Paypal_Express_Checkout_Public {

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
    public $option_array = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $setting_keys = array('woo_paypal_express_checkout_enabled', 'woo_paypal_express_checkout_sandbox', 'woo_paypal_express_checkout_rest_client_id_sandbox', 'woo_paypal_express_checkout_rest_secret_id_sandbox', 'woo_paypal_express_checkout_rest_client_id_live', 'woo_paypal_express_checkout_rest_secret_id_live', 'woo_paypal_express_checkout_landing_page', 'woo_paypal_express_checkout_button_size', 'woo_paypal_express_checkout_button_shape', 'woo_paypal_express_checkout_button_color', 'woo_paypal_express_checkout_invoice_id_prefix', 'woo_paypal_express_checkout_thank_you_page', 'woo_paypal_express_checkout_cancel_page', 'woo_paypal_express_checkout_enabled_cc');
        foreach ($setting_keys as $key => $value) {
            $this->option_array[$value] = get_option($value);
        }
        add_shortcode('wp_paypal_express_checkout', array($this, 'wp_paypal_express_checkout_display_button'), 10);
    }

    

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-paypal-express-checkout-public.js', array('jquery'), $this->version, false);
    }

    public function wp_paypal_express_checkout_display_button($atts) {
        if ($this->is_wp_express_checkout_avilable() == true) {
            try {
                $enable_cc = 'no';
                echo '<div id="paypal-button-container"></div>';
                if (!empty($this->option_array['woo_paypal_express_checkout_enabled_cc']) && $this->option_array['woo_paypal_express_checkout_enabled_cc'] == 'yes') {
                    echo '<div id="paypal-button-container-cc"></div>';
                    $enable_cc = 'yes';
                }
                $param = shortcode_atts(array(
                    'price' => '1',
                        ), $atts);

                $cancel_url_id = !empty($this->option_array['woo_paypal_express_checkout_cancel_page']) ? $this->option_array['woo_paypal_express_checkout_cancel_page'] : 1;
                $thank_you_page_id = !empty($this->option_array['woo_paypal_express_checkout_thank_you_page']) ? $this->option_array['woo_paypal_express_checkout_thank_you_page'] : 1;
                $cancel_url = get_permalink($cancel_url_id);
                $thank_you_page_url = get_permalink($thank_you_page_id);
                $details = $this->get_wp_express_checkout_api_details();
                $ENV = ($this->is_wp_express_checkout_sanbox_enable() == true) ? 'sandbox' : 'production';
                wp_enqueue_script('wp-paypal-express-checkout-js', 'https://www.paypalobjects.com/api/checkout.js', array(), null, true);
                wp_enqueue_script('wp-paypal-express-checkout-js-frontend', WP_PAYPAL_EXPRESS_CHECKOUT_ASSET_URL . '/public/js/wp-paypal-express-checkout-in-context-checkout.js', array('jquery'), $this->version, true);
                wp_localize_script('wp-paypal-express-checkout-js-frontend', 'wp_paypal_express_checkout_param', array(
                    'locale' => self::get_button_locale_code(),
                    'cancel_url' => esc_url_raw($cancel_url),
                    'return_url' => esc_url_raw($thank_you_page_url),
                    'size' => !empty($this->option_array['woo_paypal_express_checkout_button_size']) ? $this->option_array['woo_paypal_express_checkout_button_size'] : 'small',
                    'shape' => !empty($this->option_array['woo_paypal_express_checkout_button_shape']) ? $this->option_array['woo_paypal_express_checkout_button_shape'] : 'pill',
                    'color' => !empty($this->option_array['woo_paypal_express_checkout_button_color']) ? $this->option_array['woo_paypal_express_checkout_button_color'] : 'gold',
                    'env' => $ENV,
                    'price' => $param['price'],
                    'client_id' => $details['client_id'],
                    'enable_cc' => $enable_cc
                ));
            } catch (Exception $ex) {
                
            }
        }
    }

    public function is_wp_express_checkout_enable() {
        if (!empty($this->option_array['woo_paypal_express_checkout_enabled']) && $this->option_array['woo_paypal_express_checkout_enabled'] == 'yes') {
            return true;
        } else {
            return false;
        }
    }

    public function is_wp_express_checkout_avilable() {
        if ($this->get_wp_express_checkout_api_details() == false) {
            return false;
        } else {
            return true;
        }
    }

    public function get_wp_express_checkout_option() {
        return $this->option_array;
    }

    public function get_wp_express_checkout_api_details() {
        $details = false;
        if ($this->is_wp_express_checkout_sanbox_enable()) {
            if (!empty($this->option_array['woo_paypal_express_checkout_rest_client_id_sandbox']) && !empty($this->option_array['woo_paypal_express_checkout_rest_secret_id_sandbox'])) {
                $details['client_id'] = $this->option_array['woo_paypal_express_checkout_rest_client_id_sandbox'];
                $details['secret_id'] = $this->option_array['woo_paypal_express_checkout_rest_secret_id_sandbox'];
            }
        } else {
            if (!empty($this->option_array['woo_paypal_express_checkout_rest_client_id_live']) && !empty($this->option_array['woo_paypal_express_checkout_rest_secret_id_live'])) {
                $details['client_id'] = $this->option_array['woo_paypal_express_checkout_rest_client_id_live'];
                $details['secret_id'] = $this->option_array['woo_paypal_express_checkout_rest_secret_id_live'];
            }
        }
        return $details;
    }

    public function is_wp_express_checkout_sanbox_enable() {
        if (!empty($this->option_array['woo_paypal_express_checkout_sandbox']) && $this->option_array['woo_paypal_express_checkout_sandbox'] == 'yes') {
            return true;
        } else {
            return false;
        }
    }

    public static function get_button_locale_code() {
        $_supportedLocale = array(
            'en_US', 'fr_XC', 'es_XC', 'zh_XC', 'en_AU', 'de_DE', 'nl_NL',
            'fr_FR', 'pt_BR', 'fr_CA', 'zh_CN', 'ru_RU', 'en_GB', 'zh_HK',
            'he_IL', 'it_IT', 'ja_JP', 'pl_PL', 'pt_PT', 'es_ES', 'sv_SE', 'zh_TW', 'tr_TR'
        );
        $wpml_locale = self::pal_ec_get_wpml_locale();
        if ($wpml_locale) {
            if (in_array($wpml_locale, $_supportedLocale)) {
                return $wpml_locale;
            }
        }
        $locale = get_locale();
        if (!in_array($locale, $_supportedLocale)) {
            $locale = 'en_US';
        }
        return $locale;
    }

    public static function pal_ec_get_wpml_locale() {
        $locale = false;
        if (defined('ICL_LANGUAGE_CODE') && function_exists('icl_object_id')) {
            global $sitepress;
            if (isset($sitepress)) { // avoids a fatal error with Polylang
                $locale = $sitepress->get_current_language();
            } else if (function_exists('pll_current_language')) { // adds Polylang support
                $locale = pll_current_language('locale'); //current selected language requested on the broswer
            } else if (function_exists('pll_default_language')) {
                $locale = pll_default_language('locale'); //default lanuage of the blog
            }
        }
        return $locale;
    }

    public function wp_paypal_express_checkout_thankyou($content) {
        global $post;
        $txn_id = (isset($_GET['txn_id']) && !empty($_GET['txn_id'])) ? $_GET['txn_id'] : '';
        $avia_post = get_page_by_title($txn_id, 'ARRAY_A', 'pal_paypal_payment');
        if (!isset($avia_post['ID'])) {
            $thank_you_page_id = !empty($this->option_array['woo_paypal_express_checkout_thank_you_page']) ? $this->option_array['woo_paypal_express_checkout_thank_you_page'] : 1;
            if ($post->ID == $thank_you_page_id && !empty($_GET['paymentId'])) {
                $first_name = (isset($_GET['first_name']) && !empty($_GET['first_name'])) ? $_GET['first_name'] : '';
                $last_name = (isset($_GET['last_name']) && !empty($_GET['last_name'])) ? $_GET['last_name'] : '';
                $recipient_name = (isset($_GET['recipient_name']) && !empty($_GET['recipient_name'])) ? $_GET['recipient_name'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_two = (isset($_GET['addressLine2']) && !empty($_GET['addressLine2'])) ? $_GET['addressLine2'] : '';
                $city = (isset($_GET['city']) && !empty($_GET['city'])) ? $_GET['city'] : '';
                $state = (isset($_GET['state']) && !empty($_GET['state'])) ? $_GET['state'] : '';
                $postalCode = (isset($_GET['postalCode']) && !empty($_GET['postalCode'])) ? $_GET['postalCode'] : '';
                $txn_id = (isset($_GET['txn_id']) && !empty($_GET['txn_id'])) ? $_GET['txn_id'] : '';
                $total = (isset($_GET['total']) && !empty($_GET['total'])) ? $_GET['total'] : '';
                $currency = (isset($_GET['currency']) && !empty($_GET['currency'])) ? $_GET['currency'] : '';
                $paymentState = (isset($_GET['paymentState']) && !empty($_GET['paymentState'])) ? $_GET['paymentState'] : '';
                $transactionType = (isset($_GET['transactionType']) && !empty($_GET['transactionType'])) ? $_GET['transactionType'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';
                $addressLine_one = (isset($_GET['addressLine1']) && !empty($_GET['addressLine1'])) ? $_GET['addressLine1'] : '';

                $my_post = array(
                    'post_title' => $txn_id,
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                    'post_type' => 'pal_paypal_payment'
                );
                $post_id = wp_insert_post($my_post);

                foreach ($_GET as $key => $value) {
                    update_post_meta($post_id, $key, $value);
                }

                $html = '';
                $html .= '<h2>Your payment is complete</h2>';
                $html .= sprintf('<div class="hero-unit">
                        <h4> <span id="paypal-execute-details-first-name">%1$s</span>
                            <span id="paypal-execute-details-last-name">%2$s</span>, Thank you for your Order </h4>
                        <h4> Shipping Details: </h4>
                        <span id="paypal-execute-details-recipient-name">%3$s</span><br>
                        <span id="paypal-execute-details-addressLine1">%4$s</span>
                        <span id="paypal-execute-details-addressLine2">%5$s</span><br>
                        <span id="paypal-execute-details-city">%6$s</span><br>
                        <span id="paypal-execute-details-state">%7$s</span> -
                        <span id="paypal-execute-details-postal-code">%8$s</span></p>
                        <p>Transaction ID: <span id="paypal-execute-details-transaction-ID">%9$s</span></p>
                        <p>Payment Total Amount: <span id="paypal-execute-details-final-amount">%10$s</span> </p>
                        <p>Currency Code: <span id="paypal-execute-details-currency">%11$s</span></p>
                        <p>Payment Status: <span id="paypal-execute-details-payment-state">%12$s</span></p>
                        <p>Payment Type: <span id="paypal-execute-details-transaction-type">%13$s</span> </p>
                        </div>
                   ', $first_name, $last_name, $recipient_name, $addressLine_one, $addressLine_two, $city, $state, $postalCode, $txn_id, $total, $currency, $paymentState, $transactionType);
                return $content .= $html;
            }
        }
    }

}
