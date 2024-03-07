<?php
/**
 * Plugin Name: CDEK WooCommerce Shipping Method
 * Plugin URI: https://woodev.ru/downloads/wc-edostavka-integration
 * Description: Плагин расчёта стоимости доставки <a href="https://www.cdek.ru/ru/" target="_blank">СДЭК</a> для WooCommerce. Так же предоставляет возможность формирования заказа на достаку из админки, и многое другое.
 * Version: 2.2.3.0
 * Requires at least: 5.9
 * Tested up to: 6.4
 * Author: WooDev
 * WC tested up to: 8.3
 * WC requires at least: 5.6
 * Domain Path: /languages/
 * Requires PHP: 7.4
 * Author URI: https://woodev.ru
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WC_CDEK_SHIPPING_VERSION', '2.2.3.0' );
define( 'WC_CDEK_SHIPPING_FILE', __FILE__ );
define( 'WC_CDEK_SHIPPING_ACEESS_TOKEN_TRANSIENT_NAME', 'woocommerce_edostavka_access_token' );
define( 'WC_CDEK_SHIPPING_DEVELOPER_KEY', 'EP:UbtT4UMO)8Ej@y7(?-:9WM!g%-Opa' );
define( 'WC_CDEK_SHIPPING_FEE_PAYMENT_KEY', 'wc_edostavka_shipping_fee_payments' );

if ( ! class_exists( 'Woodev_Plugin_Bootstrap' ) ) {
	require_once( plugin_dir_path( WC_CDEK_SHIPPING_FILE ) . 'woodev/bootstrap.php' );
}

Woodev_Plugin_Bootstrap::instance()->register_plugin( '1.3.2', __( 'CDEK WooCommerce Shipping Method', 'woocommerce-edostavka' ), WC_CDEK_SHIPPING_FILE, 'init_wc_edostavka_shipping_init', array(
	'load_shipping_method' 	=> false,
	'minimum_wc_version'	=> '5.6',
	'minimum_wp_version'	=> '5.9',
	'backwards_compatible' 	=> '1.2.1',
) );

function init_wc_edostavka_shipping_init() {

	if( ! class_exists( 'WC_Edostavka_Shipping' ) ) {

		class WC_Edostavka_Shipping extends Woodev_Plugin {

			/** @var WD_Edostavka_Shipping_API instance */
			private $api;

			/** @var WD_Edostavka_Shipping_Admin instance */
			protected $admin;

			/** @var WC_Edostavka_Shipping single instance of this plugin */
			protected static $instance = null;

			/** plugin id */
			protected $method_id = 'edostavka';

			/** @var WC_Edostavka_Checkout instance */
			private $checkout;

			/** @var WC_Edostavka_Webhook_Handler the webhook handler instance **/
			protected $webhook_handler;

			/** @var WC_Edostavka_Cron instance */
			protected $cron;

			/** @var WC_Edostavka_Customer_Location_Data instance */
			protected $customer = null;

			public function __construct() {

				parent::__construct( $this->get_method_id(), WC_CDEK_SHIPPING_VERSION, array(
					'display_php_notice' => true,
					'text_domain'        => 'woocommerce-edostavka',
					'supported_features' => [
						'hpos'   => false,
						'blocks' => [
							'cart'     => false,
							'checkout' => false,
						],
					]
				) );

				if ( class_exists( 'WC_Integration' ) ) {

					add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
					add_action( 'woocommerce_shipping_init', array( $this, 'load_shipping_classes' ) );
					add_filter( 'woocommerce_shipping_methods', array( $this, 'include_methods' ) );
					add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );

					add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );

					add_action( 'woocommerce_shipping_zone_method_deleted', array( $this, 'delete_shipping_method' ), 10, 2 );

					$integration_settings = get_option( 'woocommerce_edostavka_settings', array() );

					if( $integration_settings ) {

						if( isset( $integration_settings['enable_debug'] ) && ! wc_string_to_bool( $integration_settings['enable_debug'] ) ) {
							remove_action( 'woodev_edostavka_api_request_performed', array( $this, 'log_api_request' ) );
						}

						if( isset( $integration_settings['auto_export_orders'] ) && wc_string_to_bool( $integration_settings['auto_export_orders'] ) && ! empty( $integration_settings['export_statuses'] ) ) {

							foreach( ( array ) $integration_settings['export_statuses'] as $export_status ) {
								$status_slug = ( 'wc-' === substr( $export_status, 0, 3 ) ) ? substr( $export_status, 3 ) : $export_status;
								add_action( 'woocommerce_order_status_' . $status_slug, array( $this, 'auto_export_order' ), 99 );
							}
						}
					}
				}
			}

			/**
			 * Main WC Edostavka Instance, ensures only one instance is/can be loaded
			 *
			 * @see wc_edstavka_shipping()
			 * @return WC_Edostavka_Shipping
			 */
			public static function instance() {
				if ( is_null( self::$instance ) ) {
					self::$instance = new self();
				}
				return self::$instance;
			}

			public function init_plugin() {

				$this->includes();
			}

			public function includes() {

				$this->webhook_handler  = $this->load_class( '/includes/webhooks/class-wc-edostavka-webhook-handler.php', 'WC_Edostavka_Webhook_Handler' );

				//API
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-api-errors.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-auth-api.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-api.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-auth-request.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-api-request.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-api-response.php' );

				//Load Dadata API
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-dadata-api.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-dadata-request.php' );
				require_once( $this->get_plugin_path() . '/api/class-wc-edostavka-dadata-response.php' );

				//Load Order Classes
				require_once( $this->get_plugin_path() . '/includes/interfaces/interface-wc-edostavka-order-item.php' );
				require_once( $this->get_plugin_path() . '/includes/interfaces/interface-wc-edostavka-order-package.php' );
				require_once( $this->get_plugin_path() . '/includes/interfaces/interface-wc-edostavka-order.php' );

				require_once( $this->get_plugin_path() . '/includes/order/class-wc-edostavka-order-factory.php' );
				require_once( $this->get_plugin_path() . '/includes/order/class-wc-edostavka-order-item.php' );
				require_once( $this->get_plugin_path() . '/includes/order/class-wc-edostavka-order-package.php' );
				require_once( $this->get_plugin_path() . '/includes/order/class-wc-edostavka-order-packages.php' );

				require_once( $this->get_plugin_path() . '/includes/functions.php' );
				require_once( $this->get_plugin_path() . '/includes/functions-api.php' );
				require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-box-packer.php' );
				require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-tariffs-data.php' );
				require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-integration.php' );
				require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-order.php' );
				require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-local-pickup-shipping-modifier.php' );

				//require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-customer-location.php' );
				require_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-customer-location-data.php' );

				$this->cron = $this->load_class( '/includes/class-wc-edostavka-cron.php', 'WC_Edostavka_Cron' );

				if ( ! is_admin() ) {
					$this->frontend_includes();
				}

				if ( is_ajax() ) {
					//$this->ajax_includes();
				}

				if ( is_admin() && ! is_ajax() ) {
					$this->admin_includes();
				}

				if( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! WC()->is_rest_api_request() ) {
					$this->ajax_includes();
				}

			}

			private function frontend_includes() {
				$this->checkout = $this->load_class( '/includes/class-wc-edostavka-checkout.php', 'WC_Edostavka_Checkout' );
			}

			private function ajax_includes() {
				include_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-ajax.php' );
			}

			private function admin_includes() {

				$this->admin = $this->load_class( '/includes/admin/class-wc-edostavka-admin.php', 'WD_Edostavka_Shipping_Admin' );

				$this->admin->message_handler = $this->get_message_handler();

				require_once( $this->get_plugin_path() . '/includes/admin/class-wc-edostavka-order-list-table.php' );
			}

			public function add_integration( $integrations ) {
				return array_merge( array( 'WC_Edostavka_Integration' ), $integrations );
			}

			public function load_shipping_classes() {
				include_once( $this->get_plugin_path() . '/includes/class-wc-edostavka-shipping-method.php' );
			}

			public function include_methods( $methods ) {
				if( class_exists( 'WD_Edostavka_Shipping' ) ) {
					$methods[ $this->get_method_id() ] = 'WD_Edostavka_Shipping';
				}
				return $methods;
			}

			/**
			 * @param array $email_classes Array Woocommerce Email Classes
			 *
			 * @return array
			 */
			public function email_classes( $email_classes ) {

				require_once( $this->get_plugin_path() . '/includes/emails/abstract-wc-edostavka-email.php' );

				if( ! class_exists( 'WC_CDEK_Tracking_Email' ) ) {
					require_once( $this->get_plugin_path() . '/includes/emails/class-wc-edostavka-tracking-email.php' );
				}

				if( ! class_exists( 'WC_CDEK_Delivered_Email' ) ) {
					require_once( $this->get_plugin_path() . '/includes/emails/class-wc-edostavka-delivered-email.php' );
				}

				if( ! class_exists( 'WC_CDEK_Not_Delivered_Email' ) ) {
					require_once( $this->get_plugin_path() . '/includes/emails/class-wc-edostavka-not-delivered-email.php' );
				}

				$email_classes['WC_CDEK_Tracking_Email']        = new WC_CDEK_Tracking_Email();
				$email_classes['WC_CDEK_Delivered_Email']       = new WC_CDEK_Delivered_Email();
				$email_classes['WC_CDEK_Not_Delivered_Email']   = new WC_CDEK_Not_Delivered_Email();

				return $email_classes;
			}

			/**
			 * @return WC_Edostavka_Integration|null
			 */
			public function get_integration_handler() {

				$integrations = WC()->integrations;

				if ( is_a( $integrations, 'WC_Integrations' ) && ! empty( $integrations->integrations[ $this->get_method_id() ] ) ) {
					return $integrations->integrations[ $this->get_method_id() ];
				}

				return null;
			}

			public function get_checkout_handler() {
				return $this->checkout;
			}

			/**
			 * @return WD_Edostavka_Shipping_Admin
			 */
			public function get_admin_instance() {
				return $this->admin;
			}

			/**
			 * Get the cron class instance.
			 *
			 * @since 2.2.0
			 * @return WC_Edostavka_Cron
			 */
			public function get_cron_instance() {
				return $this->cron;
			}

			/**
			 * Returns customer location instance class
			 *
			 * @return WC_Edostavka_Customer_Location_Data|null
			 * @throws Exception
			 * @since 2.2.0
			 */
			public function get_customer_handler() {

				if( ! $this->customer instanceof WC_Edostavka_Customer_Location_Data ) {

					$this->customer = new WC_Edostavka_Customer_Location_Data( get_current_user_id() );
					// Customer should be saved during shutdown.
					add_action( 'woocommerce_checkout_update_order_review', array( $this->customer, 'save' ), 10 );
					add_action( 'shutdown', array( $this->customer, 'save' ), 5 );
				}

				return $this->customer;
			}

			public function register_data_stores( $stores ) {

				if( ! class_exists( 'WC_Edostavka_Customer_Data_Store_Abstract') ) {
					require_once( $this->get_plugin_path() . '/includes/data-stores/abstract-wc-edostavka-customer-data-store.php' );
				}

				if( ! class_exists( 'WC_Edostavka_Customer_Data_Store') ) {
					require_once( $this->get_plugin_path() . '/includes/data-stores/class-wc-edostavka-customer-data-store.php' );
				}

				if( ! class_exists( 'WC_Edostavka_Customer_Session_Data_Store') ) {
					require_once( $this->get_plugin_path() . '/includes/data-stores/class-wc-edostavka-customer-session-data-store.php' );
				}

				if( ! isset( $stores['customer-location'] ) ) {
					$stores['customer-location'] = 'WC_Edostavka_Customer_Data_Store';
				}

				if( ! isset( $stores['customer-location-session'] ) ) {
					$stores['customer-location-session']    = 'WC_Edostavka_Customer_Session_Data_Store';
				}

				return $stores;
			}

			/**
			 * Changes global option of fee payments when shipping method deleted action is occurred
			 *
			 * @param integer   $instance_id Instance of shipping method
			 * @param string    $method_id Shipping method ID
			 *
			 * @return void
			 */
			public function delete_shipping_method( $instance_id, $method_id ) {
				if( $this->get_method_id() == $method_id ) {
					$instance_payments  = ( array ) get_option( WC_CDEK_SHIPPING_FEE_PAYMENT_KEY, array() );
					if( isset( $instance_payments[ $instance_id ] ) ) {
						unset( $instance_payments[ $instance_id ] );
						update_option( WC_CDEK_SHIPPING_FEE_PAYMENT_KEY, $instance_payments );
					}
				}
			}

			/**
			 * Get the webhook handler instance.
			 *
			 * @return WC_Edostavka_Webhook_Handler
			 */
			public function get_webhook_handler() {
				return $this->webhook_handler;
			}

			public function get_method_id() {
				return apply_filters( 'woocommerce_edostavka_shipping_id', $this->method_id );
			}

			public function get_plugin_name() {
				return __( 'CDEK WooCommerce Shipping Method', 'woocommerce-edostavka' );
			}

			public function get_download_id() {
				return 216;
			}

			protected function get_file() {
				return WC_CDEK_SHIPPING_FILE;
			}

			public function get_settings_url( $plugin_id = null ) {
				return add_query_arg( array(
					'page'		=> 'wc-settings',
					'tab'		=> 'integration',
					'section'	=> $plugin_id
				), admin_url( 'admin.php' ) );
			}

			public function is_plugin_settings() {
				return isset( $_GET['page'], $_GET['tab'] )
					&& 'wc-settings' === $_GET['page']
					&& 'integration' === $_GET['tab']
					&& ( ! isset( $_GET['section'] ) || $this->get_method_id() === $_GET['section'] );
			}

			public function get_documentation_url() {
				return 'https://woodev.ru/docs/nastrojka-plagina-integratsii-sdek-s-woocommerce-versii-2-2';
			}

			public function get_sales_page_url() {
				return add_query_arg( array(
					'utm_source' => str_replace( '.', '_', wp_parse_url( home_url(), PHP_URL_HOST ) ),
					'utm_medium' => 'organic'
				), 'https://woodev.ru/downloads/wc-edostavka-integration' );
			}

			public function get_support_url() {
				$args = array(
					'wpf4766_3'	=> urlencode( 'Проблемы с плагином' ),
					'wpf4766_5'	=> $this->get_download_id(),
					'wpf4766_7'	=> site_url(),
					'utm_source' => str_replace( '.', '_', wp_parse_url( home_url(), PHP_URL_HOST ) ),
					'utm_medium' => 'organic'
				);
				return add_query_arg( $args, 'https://woodev.ru/support/' );
			}

			public function auto_export_order( $order_id ) {

				$order = new WC_Edostavka_Shipping_Order( $order_id );

				if( $order->has_edostavka_shipping() && ! $order->is_exported() && $order->export_action() ) {

					$order->add_order_note( __( 'The order was automatically exported to CDEK successfully.', 'woocommerce-edostavka' ) );

				}

			}

			public function add_admin_notices() {

				parent::add_admin_notices();

				$screen = get_current_screen();

				if ( ( ( $screen && 'plugins' === $screen->id ) || $this->is_plugin_settings() ) ) {

					if( 'yes' === get_option( 'wc_edostavka_upgraded_to_2_2_2_0' ) ) {

						$this->get_admin_notice_handler()->add_admin_notice(
							sprintf(
								'Вы обновили плагин <strong>%1$s</strong> до версии 2.2.2.0, проверьте активирован ли ваш %2$sлицензионный ключ%3$s. При обновлении он мог деактивироваться.',
								$this->get_plugin_name(),
								sprintf( '<a href="%s">', esc_url( $this->get_license_instance()->get_license_settings_url() ) ),
								'</a>'
							),
							'wc_edostavka_upgraded_to_2_2_2_0',
							array(
								'always_show_on_settings' => false,
								'notice_class'            => 'updated'
							)
						);

					} elseif( $this->get_documentation_url() ) {

						$this->get_admin_notice_handler()->add_admin_notice(
							sprintf( 'Спасибо за установку плагина <strong>%1$s</strong>! Для начала работы, пожалуйста ознакомтесь с %2$sдокументацией%3$s. А так же, незабудьте %4$sуказать ваш лицензионный ключ%3$s.',
								$this->get_plugin_name(),
								sprintf( '<a href="%s" target="_blank">', esc_url( $this->get_documentation_url() ) ),
								'</a>',
								sprintf( '<a href="%s">', esc_url( $this->get_license_instance()->get_license_settings_url() ) )
							),
							'get-started-notice',
							array( 'always_show_on_settings' => false, 'notice_class' => 'updated' )
						);
					}
				}

				if( ! class_exists( 'WC_Integration' ) && ( $screen && 'plugins' === $screen->id ) ) {
					$error_message = sprintf( 'Дальнейшая работа плагина <strong>%s</strong> не возможна. Недоступен класс WC_Integration. Убедитесь что у вас установлена последняя версия <a href="%s" target="_blank">Woocommerce</a>.', $this->get_plugin_name(), esc_url( 'https://woocommerce.com/' ) );
					$this->get_admin_notice_handler()->add_admin_notice( $error_message, 'wc-integration-class-missing', array(
						'notice_class' => 'error',
					) );
				}
			}

			/**
			 * Saves errors or messages to WooCommerce Log (woocommerce/logs/edostavka-xxx.txt)
			 *
			 * @see Woodev_Plugin::log()
			 * @param string $message the message to log
			 * @param string $log_id log id
			 */
			public function log( $message, $log_id = null ) {

				if ( wc_string_to_bool( $this->get_integration_handler()->get_option( 'enable_debug', 'no' ) ) ) {
					parent::log( $message, $log_id );
				}
			}

			/**
			 * @return WD_Edostavka_Shipping_API
			 */
			public function get_api() {
				if ( is_object( $this->api ) ) {
					return $this->api;
				}

				return $this->api = new WD_Edostavka_Shipping_API();
			}

			/**
			 * Builds the lifecycle handler instance.
			 */
			protected function init_lifecycle_handler() {

				require_once( $this->get_plugin_path() . '/includes/class-lifecycle.php' );

				$this->lifecycle_handler = new WC_Edostavka_Lifecycle( $this );
			}
		}
	}

	/**
	 * @return WC_Edostavka_Shipping|null
	 */
	function wc_edostavka_shipping() {
		return WC_Edostavka_Shipping::instance();
	}

	wc_edostavka_shipping();
}
