<?php
/**
 * Plugin Name: Dolyame payment
 * Plugin URI: https://wordpress.org/plugins/dolyame-payment
 * Description: Dolyame Payments Gateway for WooCommerce
 * Version: 2.1.2
 * Author: «Долями»
 * Author URI: https://dolyame.ru
 * Copyright: © 2021, «Долями».
 * Text Domain: dolyame_payment
 * Domain Path: /languages
 */

class WC_Dolyamepayment
{
	private $settings;
	private $plugin_page;

	public function __construct()
	{
		$this->settings = get_option('woocommerce_dolyamepayment_settings');

		if (empty($this->settings['login'])) {
			$this->settings['login'] = '';
		}
		if (empty($this->settings['password'])) {
			$this->settings['password'] = '';
		}

		if (empty($this->settings['enabled'])) {
			$this->settings['enabled'] = 'no';
		}

		add_action('plugins_loaded', [$this, 'initGateway']);
	}

	public function initGateway()
	{
		if (!class_exists('WC_Payment_Gateway')) {
			return;
		}

		load_plugin_textdomain('dolyame_payment', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		include_once __DIR__ . '/includes/class-wc-gateway-dolyamepayment.php';

		add_filter('woocommerce_payment_gateways', [$this, 'addGateway']);

		if ($this->settings['enabled'] == 'no') {
			return;
		}

		// Disable for subscriptions until supported
		if (!is_admin() && class_exists('WC_Subscriptions_Cart') && WC_Subscriptions_Cart::cart_contains_subscription() && 'no' === get_option(WC_Subscriptions_Admin::$option_prefix . '_accept_manual_renewals', 'no')) {
			return;
		}

	}

	public function addGateway($methods)
	{
		$methods[] = 'WC_Gateway_dolyamepayment';
		return $methods;
	}

}
$GLOBALS['wc_dolyamepayment'] = new WC_dolyamepayment();
