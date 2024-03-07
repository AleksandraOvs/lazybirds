<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

require_once('tinkoff/TinkoffMerchantAPI.php');
require_once('tinkoff/language/ru/RuLanguage.php');
require_once('tinkoff/language/Language.php');
require_once('tinkoff/RecurrentPaymentTinkoff.php');

/**
 * Plugin Name: Тинькофф Банк
 * Plugin URI: https://oplata.tinkoff.ru/
 * Description: Проведение платежей через Tinkoff Oplata
 * Version: 2.0.0
 * Author: Tinkoff
 */


/* Add a custom payment class to WC
  ------------------------------------------------------------ */
register_activation_hook(__FILE__, 'create_table_recurrent_tinkoff');

add_action('plugins_loaded', 'woocommerce_tinkoff', 0);

function create_table_recurrent_tinkoff()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "recurrent_tinkoff";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
              id int(10) NOT NULL AUTO_INCREMENT,
              rebillId VARCHAR (15) NOT NULL,
              paymentId int(10) NOT NULL,
              PRIMARY KEY  (id)
            ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function woocommerce_tinkoff()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    } // if the WC payment gateway class is not available, do nothing
    if (class_exists('WC_Tinkoff')) {
        return;
    }

    class WC_Tinkoff extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $plugin_dir = plugin_dir_url(__FILE__);

            global $woocommerce;

            $this->id = 'tinkoff';
            $this->icon = apply_filters('woocommerce_tinkoff_icon', '' . $plugin_dir . 'tinkoff/tinkoff.png');
            $this->has_fields = false;

            // Load the settings
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            if ($this->get_option('payment_form_language') == 'en') {
                $this->icon = apply_filters('woocommerce_tinkoff_icon', '' . $plugin_dir . 'tinkoff/tinkoff-en.png');
                $this->title = 'Tinkoff Bank';
                $this->description = 'Payment via www.tinkoff.ru';
            }
            $this->email_company = $this->get_option('email_company');
            $this->payment_method_ffd = $this->get_option('payment_method_ffd');
            $this->payment_object_ffd = $this->get_option('payment_object_ffd');
            $this->taxation = $this->get_option('taxation');
            $this->instructions = $this->get_option('instructions');
            $this->check_data_tax = $this->get_option('check_data_tax');

            // Actions
            add_action('woocommerce_receipt_tinkoff', array($this, 'receipt_page'));

            // Save options
            add_action('woocommerce_update_options_payment_gateways_tinkoff', array($this, 'process_admin_options'));

            // Payment listener/API hook
            add_action('woocommerce_api_wc_tinkoff', array($this, 'check_assistant_response'));

            if (!$this->is_valid_for_use()) {
                $this->enabled = false;
            }

            $this->supports = array_merge(
                $this->supports,
                array(
                    'subscriptions',
                    'subscription_cancellation',
                    'subscription_reactivation',
                    'subscription_suspension',
                    'multiple_subscriptions',
                    'subscription_payment_method_change_customer',
                    'subscription_payment_method_change_admin',
                    'subscription_amount_changes',
                    'subscription_date_changes',
                )
            );

            $this->_maybe_register_callback_in_subscriptions_t();
        }

        /**
         * Check if this gateway is enabled and available in the user's country
         */
        function is_valid_for_use()
        {
            return true;
        }

        protected function _maybe_register_callback_in_subscriptions_t()
        {
            add_action('woocommerce_scheduled_subscription_payment_' . $this->id, array($this, 'scheduled_subscription_payment'), 10, 2);
        }

        public function scheduled_subscription_payment($amount, $order)
        {
            $setting = array(
                "email_company"         => $this->get_option('email_company'),
                "payment_method_ffd"    => $this->get_option('payment_method_ffd'),
                "check_data_tax"        => $this->get_option('check_data_tax'),
                "taxation"              => $this->get_option('taxation'),
                "payment_form_language" => $this->get_option('payment_form_language'),
                "merchant_id"           => $this->get_option('merchant_id'),
                "secret_key"            => $this->get_option('secret_key'),
            );
            $recurrentPayment = new RecurrentPaymentTinkoff($amount, $order);
            $recurrentPayment->recurrentTinkoff($setting);
        }

        /**
         * Форма оплаты
         **/
        function receipt_page($order_id)
        {
            $order = new WC_Order($order_id);
            $setting = array(
                "email_company"         => $this->get_option('email_company'),
                "payment_method_ffd"    => $this->get_option('payment_method_ffd'),
                "payment_object_ffd"    => $this->get_option('payment_object_ffd'),
                "check_data_tax"        => $this->get_option('check_data_tax'),
                "taxation"              => $this->get_option('taxation'),
                "payment_form_language" => $this->get_option('payment_form_language'),
            );

            $supportPaymentTinkoff = new SupportPaymentTinkoff($setting);
            $arrFields = SupportPaymentTinkoff::send_data($order, $order_id);

            if (!function_exists('get_plugins')) {
                // подключим файл с функцией get_plugins()
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            // получим данные плагинов
            $all_plugins = get_plugins();

            foreach ($all_plugins as $key => $plugin) {
                preg_match_all('#woocommerce-subscriptions-[0-9.-]+\/woocommerce-subscriptions.php#uis', $key, $pluginSubscriptions);

                if (!empty($pluginSubscriptions[0])) {
                    // активирован ли плагин woocommerce-subscriptions
                    if (is_plugin_active($key)) {

                        if (wcs_order_contains_subscription($order)) {
                            $arrFields['Recurrent'] = "Y";
                            $arrFields['CustomerKey'] = (string)$order->get_user_id();
                        }
                    }
                }
            }

            $arrFields = SupportPaymentTinkoff::get_setting_language($arrFields);

            $Tinkoff = new TinkoffMerchantAPI($this->get_option('merchant_id'), $this->get_option('secret_key'));
            $request = $Tinkoff->buildQuery('Init', $arrFields);

            SupportPaymentTinkoff::logs($arrFields, $request);

            $request = json_decode($request);

            if (!empty($this->payment_system_name)) {
                $arrFields['payment_system_name'] = $this->payment_system_name;
            }

            foreach ($arrFields as $strFieldName => $strFieldValue) {
                $args_array[] = '<input type="hidden" name="' . esc_attr($strFieldName) . '" value="' . esc_attr($strFieldValue) . '" />';
            }

            if (isset($request->PaymentURL)) {
                try {
                    wc_reduce_stock_levels($order_id);
                } catch (Exception $e) {

                }
                setcookie('tinkoffReturnUrl', $this->get_return_url($order), time() + 3600, "/");
                wp_redirect($request->PaymentURL);
            } else {
                echo '<p>' . Language::get(Language::REQUEST_TO_PAYMENT) . '</p>';
            }
        }


        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         *
         * @since 0.1
         **/
        public function admin_options()
        {
            ?>
            <h3><?php _e('Tinkoff', 'woocommerce'); ?></h3>
            <p><?php _e(Language::get(Language::SETUP_OF_RECEIVING), 'woocommerce'); ?></p>

            <?php if ($this->is_valid_for_use()) : ?>

            <table class="form-table">
                <?php
                // Generate the HTML For the settings form.
                $this->generate_settings_html();
                ?>
            </table>

        <?php else : ?>
            <div class="inline error"><p>
                    <strong><?php _e(Language::get(Language::GATEWAY_IS_DISABLED),
                            'woocommerce'); ?></strong>: <?php _e(Language::get(Language::TINKOFF_DOES_NOT_SUPPORT),
                        'woocommerce'); ?>
                </p></div>
        <?php
        endif;

        } //End admin_options()

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @access public
         * @return void
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled'        => array(
                    'title'       => __(Language::get(Language::PAYMENT_METHOD), 'woocommerce'),
                    'type'        => 'checkbox',
                    'label'       => __(Language::get(Language::ACTIVE), 'woocommerce'),
                    'default'     => 'yes'
                ),
                'title'          => array(
                    'title'       => __(Language::get(Language::PAYMENT_METHOD_NAME), 'woocommerce'),
                    'type'        => 'text',
                    'description' => __(Language::get(Language::PAYMENT_METHOD_USER), 'woocommerce'),
                    'default'     => __(Language::get(Language::TINKOFF_BANK), 'woocommerce')
                ),
                'merchant_id'    => array(
                    'title'       => __(Language::get(Language::TERMINAL), 'woocommerce'),
                    'type'        => 'text',
                    'description' => __(Language::get(Language::SPECIFIED_PERSONAL), 'woocommerce'),
                    'default'     => ''
                ),
                'secret_key'     => array(
                    'title'       => __(Language::get(Language::PASSWORD), 'woocommerce'),
                    'type'        => 'text',
                    'description' => __(Language::get(Language::SPECIFIED_PERSONAL), 'woocommerce'),
                    'default'     => ''
                ),
                'email_company'  => array(
                    'type'        => 'text',
                    'title'       => __(Language::get(Language::EMAIL_COMPANY), 'woocommerce'),
                    'default'     => '',
                ),
                'payment_method_ffd' => array(
                    'type'        => 'select',
                    'title'       => __(Language::get(Language::PAYMENT_METHOD_FFD), 'woocommerce'),
                    'description' => __(Language::get(Language::PAYMENT_METHOD_FFD)),
                    'default'     => 'error',
                    'options'     => array(
                        'error'                 => __('', 'woocommerce'),
                        'full_prepayment'       => __(Language::get(Language::FULL_PREPAYMENT), 'woocommerce'),
                        'prepayment'            => __(Language::get(Language::PREPAYMENT), 'woocommerce'),
                        'advance'               => __(Language::get(Language::ADVANCE), 'woocommerce'),
                        'full_payment'          => __(Language::get(Language::FULL_PAYMENT), 'woocommerce'),
                        'partial_payment'       => __(Language::get(Language::PARTIAL_PAYMENT), 'woocommerce'),
                        'credit'                => __(Language::get(Language::CREDIT), 'woocommerce'),
                        'credit_payment'        => __(Language::get(Language::CREDIT_PAYMENT), 'woocommerce'),
                    ),
                ),
                'payment_object_ffd'  => array(
                    'type'         => 'select',
                    'title'        => __(Language::get(Language::PAYMENT_OBJECT_FFD), 'woocommerce'),
                    'description'  => __(Language::get(Language::PAYMENT_OBJECT_FFD)),
                    'default'      => 'error',
                    'options'      => array(
                        'error'                 => __('', 'woocommerce'),
                        'commodity'             => __(Language::get(Language::COMMODITY), 'woocommerce'),
                        'excise'                => __(Language::get(Language::EXCISE), 'woocommerce'),
                        'job'                   => __(Language::get(Language::JOB), 'woocommerce'),
                        'service'               => __(Language::get(Language::SERVICE), 'woocommerce'),
                        'gambling_bet'          => __(Language::get(Language::GAMBLING_BET), 'woocommerce'),
                        'gambling_prize'        => __(Language::get(Language::GAMBLING_PRIZE), 'woocommerce'),
                        'lottery'               => __(Language::get(Language::LOTTERY), 'woocommerce'),
                        'lottery_prize'         => __(Language::get(Language::LOTTERY_PRIZE), 'woocommerce'),
                        'intellectual_activity' => __(Language::get(Language::INTELLECTUAL_ACTIVITY), 'woocommerce'),
                        'payment'               => __(Language::get(Language::PAYMENT), 'woocommerce'),
                        'agent_commission'      => __(Language::get(Language::AGENT_COMMISSION), 'woocommerce'),
                        'composite'             => __(Language::get(Language::COMPOSITE), 'woocommerce'),
                        'another'               => __(Language::get(Language::ANOTHER), 'woocommerce'),
                    ),
                ),
                'description'      => array(
                    'title'                     => __('Description', 'woocommerce'),
                    'type'                      => 'textarea',
                    'description'               => __(Language::get(Language::DESCRIPTION_PAYMENT_METHOD),
                        'woocommerce'),
                    'default'                   => Language::get(Language::PAYMENT_THROUGH)
                ),
                'auto_complete'    => array(
                    'title'                     => __(Language::get(Language::ORDER_COMPLETION), 'woocommerce'),
                    'type'                      => 'checkbox',
                    'label'                     => __(Language::get(Language::AUTOMATIC_SUCCESSFUL),
                        'woocommerce'),
                    'description'               => __('', 'woocommerce'),
                    'default'                   => '0'
                ),
                'check_data_tax'   => array(
                    'title'                     => __(Language::get(Language::SEND_DATA_CHECK), 'woocommerce'),
                    'type'                      => 'checkbox',
                    'label'                     => __(Language::get(Language::DATA_TRANSFER), 'woocommerce'),
                    'description'               => __(Language::get(Language::SEND_DATA_CHECK), 'woocommerce'),
                    'default'                   => '0'
                ),
                'taxation'         => array(
                    'title'         => __(Language::get(Language::TAX_SYSTEM), 'woocommerce'),
                    'type'          => 'select',
                    'description'   => __(Language::get(Language::CHOOSE_SYSTEM_STORE)),
                    'default'       => 'error',
                    'options'       => array(
                        'error'                 => __('', 'woocommerce'),
                        'osn'                   => __(Language::get(Language::TOTAL_CH), 'woocommerce'),
                        'usn_income'            => __(Language::get(Language::SIMPLIFIED_CH), 'woocommerce'),
                        'usn_income_outcome'    => __(Language::get(Language::SIMPLIFIED__COSTS), 'woocommerce'),
                        'envd'                  => __(Language::get(Language::SINGLE_IMPUTED_INCOME), 'woocommerce'),
                        'esn'                   => __(Language::get(Language::UNIFIED_AGRICULTURAL_TAX), 'woocommerce'),
                        'patent'                => __(Language::get(Language::PATENT_CH), 'woocommerce'),
                    ),
                ),
                'payment_form_language'=> array(
                    'title'             => __(Language::get(Language::PAYMENT_LANGUAGE), 'woocommerce'),
                    'type'              => 'select',
                    'description'       => __(Language::get(Language::CHOOSE_PAYMENT_LANGUAGE)),
                    'default'           => 'ru',
                    'options'           => array(
                        'ru'                    => __(Language::get(Language::RUSSIA), 'woocommerce'),
                        'en'                    => __(Language::get(Language::ENGLISH), 'woocommerce'),
                    ),
                ),
            );

        }

        /**
         * Дополнительная информация в форме выбора способа оплаты
         **/
        function payment_fields()
        {
            if ($this->description) {
                echo wpautop(wptexturize($this->description));
            }
        }

        /**
         * Process the payment and return the result
         **/
        function process_payment($order_id)
        {
            $order = new WC_Order($order_id);

            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url($order)
            );
        }

        /**
         * Check Response
         **/
        function check_assistant_response()
        {
            global $woocommerce;

            if (!empty($_POST)) {
                $arrRequest = $_POST;
            } else {
                $arrRequest = $_GET;
            }

            $objOrder = new WC_Order($arrRequest['pg_order_id']);

            $arrResponse = array();
            $aGoodCheckStatuses = array('pending', 'processing');
            $aGoodResultStatuses = array('pending', 'processing', 'completed');

            switch ($_GET['type']) {
                case 'check':
                    $bCheckResult = 1;
                    if (empty($objOrder) || !in_array($objOrder->status, $aGoodCheckStatuses)) {
                        $bCheckResult = 0;
                        $error_desc = 'Order status ' . $objOrder->status . ' or deleted order';
                    }
                    if (intval($objOrder->order_total) != intval($arrRequest['pg_amount'])) {
                        $bCheckResult = 0;
                        $error_desc = 'Wrong amount';
                    }

                    $arrResponse['pg_salt'] = $arrRequest['pg_salt'];
                    $arrResponse['pg_status'] = $bCheckResult ? 'ok' : 'error';
                    $arrResponse['pg_error_description'] = $bCheckResult ? "" : $error_desc;

                    $objResponse = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
                    $objResponse->addChild('pg_salt', $arrResponse['pg_salt']);
                    $objResponse->addChild('pg_status', $arrResponse['pg_status']);
                    $objResponse->addChild('pg_error_description', $arrResponse['pg_error_description']);
                    $objResponse->addChild('pg_sig', $arrResponse['pg_sig']);
                    break;

                case 'result':
                    if (intval($objOrder->order_total) != intval($arrRequest['pg_amount'])) {
                        $strResponseDescription = 'Wrong amount';
                        if ($arrRequest['pg_can_reject'] == 1) {
                            $strResponseStatus = 'rejected';
                        } else {
                            $strResponseStatus = 'error';
                        }
                    } elseif ((empty($objOrder) || !in_array($objOrder->status, $aGoodResultStatuses)) &&
                        !($arrRequest['pg_result'] == 0 && $objOrder->status == 'failed')
                    ) {
                        $strResponseDescription = 'Order status ' . $objOrder->status . ' or deleted order';
                        if ($arrRequest['pg_can_reject'] == 1) {
                            $strResponseStatus = 'rejected';
                        } else {
                            $strResponseStatus = 'error';
                        }
                    } else {
                        $strResponseStatus = 'ok';
                        $strResponseDescription = "Request cleared";

                        if ($arrRequest['pg_result'] == 1) {
                            $objOrder->update_status('completed', __(Language::get(Language::PAYMENT_SUCCESS), 'woocommerce'));
                            WC()->cart->empty_cart();
                        } else {
                            $objOrder->update_status('failed', __(Language::get(Language::PAYMENT_NOT_SUCCESS), 'woocommerce'));
                            WC()->cart->empty_cart();
                        }
                    }

                    $objResponse = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
                    $objResponse->addChild('pg_salt', $arrRequest['pg_salt']);
                    $objResponse->addChild('pg_status', $strResponseStatus);
                    $objResponse->addChild('pg_description', $strResponseDescription);

                    break;
                case 'success':
                    wp_redirect($this->get_return_url($objOrder));
                    break;
                case 'failed':
                    wp_redirect($objOrder->get_cancel_order_url());
                    break;
                default :
                    die('wrong type');
            }

            header("Content-type: text/xml");
            echo $objResponse->asXML();
            die();
        }

        function showMessage($content)
        {
            return '
        <h1>' . $this->msg['title'] . '</h1>
        <div class="box ' . $this->msg['class'] . '-box">' . $this->msg['message'] . '</div>
        ';
        }

        function showTitle($title)
        {
            return false;
        }
    }

    /**
     * Add the gateway to WooCommerce
     **/
    function add_tinkoff_gateway($methods)
    {
        $methods[] = 'WC_Tinkoff';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_tinkoff_gateway');
}

/////////////// success page

add_filter('query_vars', 'tinkoff_success_query_vars');
function tinkoff_success_query_vars($query_vars)
{
    $query_vars[] = 'tinkoff_success';
    return $query_vars;
}


add_action('parse_request', 'tinkoff_success_parse_request');
function tinkoff_success_parse_request(&$wp)
{
    if (array_key_exists('tinkoff_success', $wp->query_vars)) {
        $a = new WC_Tinkoff();
        add_action('the_title', array($a, 'showTitle'));
        add_action('the_content', array($a, 'showMessage'));

        if ($wp->query_vars['tinkoff_success'] == 1) {
            if (isset($_COOKIE['tinkoffReturnUrl'])) {
                $tinkoffReturnUrl = $_COOKIE['tinkoffReturnUrl'];
                unset($_COOKIE['tinkoffReturnUrl']);
                echo "<script language=\"javascript\" type=\"text/javascript\">document.location.replace('" . $tinkoffReturnUrl . "');</script>";
            } else {
                $a->msg['title'] = Language::get(Language::PAYMENT_SUCCESS);
                $a->msg['message'] = Language::get(Language::PAYMENT_THANK);
                $a->msg['class'] = 'woocommerce_message woocommerce_message_info';
                WC()->cart->empty_cart();
            }
        } else {
            $a->msg['title'] = Language::get(Language::PAYMENT_NOT_SUCCESS);
            $a->msg['message'] = Language::get(Language::PAYMENT_ERROR);
            $a->msg['class'] = 'woocommerce_message woocommerce_message_info';
        }
    }
    return;
}

/////////////// success page end
?>
