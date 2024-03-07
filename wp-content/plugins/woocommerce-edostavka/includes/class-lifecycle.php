<?php

defined( 'ABSPATH' ) or exit;

class WC_Edostavka_Lifecycle extends Woodev_Lifecycle {

	/**
	 * @param WC_Edostavka_Shipping $plugin
	 */
	public function __construct( Woodev_Plugin $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = array(
			'2.2.0',
			'2.2.0.6',
			'2.2.2.0'
		);
	}

	protected function upgrade_to_2_2_0() {
		//Getting legacy settings
		$settings       = get_option( 'woocommerce_edostavka-integration_settings' );
		$license_key    = isset( $settings['license_key'] ) ? trim( $settings['license_key'] ) : null;

		$settings_keys = array(
			'api_login'                          => 'api_login',
			'api_password'                       => 'api_password',
			'default_state_id'                   => 'customer_default_city',
			'sender_address'                     => 'seller_address',
			'sender_phone'                       => 'sender_phone',
			'order_prefix'                       => 'order_prefix',
			'hide_single_counrty'                => 'hide_single_country',
			'required_address_1'                 => 'disable_address_field',
			'enable_custom_city'                 => 'enable_custom_city',
			'minimum_height'                     => 'default_height',
			'minimum_width'                      => 'default_width',
			'minimum_length'                     => 'default_length',
			'popup_map_action_button_color'      => 'action_button_color',
			'choose_delivery_point_button_color' => 'choose_button_color',
			'enable_suggestions_address'         => 'enable_suggestions_address',
			'dadata_token'                       => 'dadata_token',
			'dadata_secret'                      => 'dadata_secret'
		);

		foreach ( $settings_keys as $legacy_key => $new_key ) {
			if( isset( $settings[ $legacy_key ] ) && ! empty( $settings[ $legacy_key ] ) ) {
				$this->get_plugin()->get_integration_handler()->update_option( $new_key, $settings[ $legacy_key ] );
			}
		}

		if( ! is_null( $license_key ) ) {
			$this->get_plugin()->get_license_instance()->verify_license( $license_key );
		}

		if( $this->get_plugin()->get_integration_handler()->is_configured() ) {
			$this->get_plugin()->get_webhook_handler()->set_webhooks();
		}
	}

	protected function upgrade_to_2_2_0_6() {
		global $wpdb;

		if( $wpdb->actionscheduler_groups && $wpdb->actionscheduler_actions ) {
			$group_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$wpdb->actionscheduler_groups} WHERE slug=%s", 'wc_edostavka_location_cities' ) );
			if( $group_id ) {
				$wpdb->delete( $wpdb->actionscheduler_actions, array( 'group_id' => $group_id ), array( '%d' ) );
			}
		}
	}

	protected function upgrade_to_2_2_2_0() {

		$legacy_license_key = get_option( 'cdek_woocommerce_shipping_method_license_key', '' );

		if ( ! empty( $legacy_license_key ) ) {

			update_option( $this->get_plugin()->get_plugin_option_name( 'license_key' ), $legacy_license_key );

			$this->get_plugin()->get_license_instance()->verify_license( $legacy_license_key );

			delete_option( 'cdek_woocommerce_shipping_method_license_key' );
			delete_option( 'cdek_woocommerce_shipping_method_license' );
		}

		update_option( 'wc_edostavka_upgraded_to_2_2_2_0', 'yes' );
	}

	public function activate() {
		if( $this->get_plugin()->is_plugin_active( 'wc-edostavka.php' ) ) {
			deactivate_plugins( array( 'wc-edostavka/wc-edostavka.php' ) );
		}
	}

	protected function install() {
		if( $this->get_plugin()->get_integration_handler()->is_configured() ) {
			$this->get_plugin()->get_webhook_handler()->set_webhooks();
		}
	}

	public function deactivate() {}
}
