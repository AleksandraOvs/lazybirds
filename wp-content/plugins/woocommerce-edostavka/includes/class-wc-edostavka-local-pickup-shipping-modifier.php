<?php

defined( 'ABSPATH' ) or exit;

class WC_Edostavka_Local_Pickup_Shipping_Modifier {

	public function __construct() {
		add_filter( 'woocommerce_shipping_instance_form_fields_local_pickup', array( $this, 'shipping_settings' ) );
		add_filter( 'woocommerce_shipping_local_pickup_is_available', array( $this, 'availability' ), 10, 3 );
		add_filter( 'woocommerce_shipping_local_pickup_instance_settings_values', array( $this, 'settings_values' ), 10, 2 );
		add_filter( 'woocommerce_shipping_method_supports', array( $this, 'method_supports' ), 10, 3 );

		add_filter( 'woocommerce_default_address_fields', array( $this, 'default_address_fields' ) );
		add_filter( 'edostavka_update_order_review_state_args', array( $this, 'refresh_fields' ) );
		add_filter( 'edostavka_update_order_review_address_args', array( $this, 'refresh_fields' ) );
		add_filter( 'edostavka_update_order_review_postcode_args', array( $this, 'refresh_fields' ) );

		if ( is_admin() && ! wp_doing_ajax() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'add_admin_styles' ) );
		}
	}

	public function load_scripts( $hook ) {
		if( 'woocommerce_page_wc-settings' == $hook && $this->is_shipping_settings() ) {
			wp_enqueue_script( 'edostavka-local-pickup-script', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/admin/local-pickup-shipping.js', array( 'jquery', 'selectWoo' ), WC_CDEK_SHIPPING_VERSION, true );
		}
	}

	/**
	 * Adjusts enhanced select styles in shipping modal.
	 *
	 * @internal
	 */
	public function add_admin_styles() {
		if ( $this->is_shipping_settings() ) {
			wp_add_inline_style( 'woocommerce_admin_styles', '.wc-backbone-modal .select2-container{ max-width: 350px !important; }' );
		}
	}

	/**
	 * Modifies local pickup shipping settings.
	 *
	 * @internal
	 *
	 * @param array $fields the shipping settings fields
	 * @return array updated fields
	 */
	public function shipping_settings( $fields ) {

		$zone = false;

		if( isset( $_REQUEST['zone_id'] ) ) {
			$zone = WC_Shipping_Zones::get_zone( wc_clean( $_REQUEST['zone_id'] ) );
		} elseif( isset( $_REQUEST['instance_id'] ) ) {
			$zone = WC_Shipping_Zones::get_zone_by( 'instance_id', wc_clean( $_REQUEST['instance_id'] ) );
		}

		if( ! $zone ) {
			return $fields;
		}

		$zone_countries = wp_list_pluck( wp_list_filter( $zone->get_zone_locations(), array( 'type' => 'country' ) ), 'code' );

		$fields['allowed_cities'] = array(
			'title'    => 'Доступные города',
			'type'     => 'multiselect',
			'class'    => 'wc-enhanced-select',
			'css'      => 'min-width: 250px;',
			'default'  => '',
			'desc_tip' => 'Выберите в каких городах будет доступен этот метод доставки. Оставьте пустым чтобы не использовать.',
			'options'  => array(),
			'custom_attributes' => array(
				'data-placeholder'  => 'Выберите города',
				'data-countries'    => implode( ',', $zone_countries )
			)
		);

		$fields['disabled_fields'] = array(
			'title'    => 'Отключать поля',
			'type'     => 'multiselect',
			'class'    => 'wc-enhanced-select-nostd',
			'css'      => 'min-width: 250px;',
			'default'  => '',
			'desc_tip' => 'Выберите в какие поля формы оформления заказа нужно отключать при выборе этого метода доставки. Оставьте пустым чтобы не использовать.',
			'options'  => array(
				'state'     => 'Регион',
				'address'   => 'Адрес',
				'postcode'  => 'Почтовый индекс'
			),
			'custom_attributes' => array(
				'data-placeholder'  => 'Выберите поля формы заказа'
			)
		);

		if( isset( $_REQUEST['instance_id'] ) ) {
			$instance_id = absint( $_REQUEST['instance_id'] );
			$settings = get_option( sprintf( 'woocommerce_local_pickup_%d_settings', $instance_id ), array() );

			if( isset( $settings['allowed_cities'], $settings['saved_cities'] ) ) {

				foreach ( ( array ) $settings['allowed_cities'] as $code ) {

					if( ! isset( $settings['saved_cities'][ $code ] ) || ! isset( $settings['saved_cities'][ $code ]['city'] ) ) continue;

					$strings = array();
					$strings[] = $settings['saved_cities'][ $code ]['city'];

					if( ! empty( $settings['saved_cities'][ $code ]['region'] ) ) {
						$strings[] = $settings['saved_cities'][ $code ]['region'];
					}

					if( ! empty( $settings['saved_cities'][ $code ]['sub_region'] ) ) {
						$strings[] = $settings['saved_cities'][ $code ]['sub_region'];
					}

					$fields['allowed_cities']['options'][ $code ] = implode( ', ', $strings );
				}
			}
		}

		return $fields;
	}

	public function settings_values( $settings, $method ) {

		if( $method instanceof WC_Shipping_Local_Pickup ) {

			if( isset( $settings['allowed_cities'] ) && ! empty( $settings['allowed_cities'] ) ) {

				$current_data = isset( $settings['saved_cities'] ) ? $settings['saved_cities'] : array();

				foreach ( ( array ) $settings['allowed_cities'] as $city_code ) {
					if( in_array( $city_code, array_keys( $current_data ), true ) ) {
						$settings['saved_cities'][ $city_code ] = $current_data[ $city_code ];
					} else {

						$locations = wc_edostavka_get_location_cities( array(
							'code' => $city_code,
							'size' => 1
						) );

						if( $locations && ! empty( $locations[0] ) ) {
							$settings['saved_cities'][ $locations[0]->code ] = array(
								'city'          => $locations[0]->city,
								'region_code'   => $locations[0]->region_code,
								'country_code'  => $locations[0]->country_code,
								'country'       => $locations[0]->country,
								'region'        => $locations[0]->region,
								'sub_region'    => isset( $locations[0]->sub_region ) ? $locations[0]->sub_region : null
							);
						}
					}
				}

			} else {
				$settings['saved_cities'] = array();
			}
		}

		return $settings;
	}

	/**
	 * Determines if local pickup shipping should be available or not based on cities criteria.
	 *
	 * @internal
	 *
	 * @param bool $available true if local pickup shipping is available
	 * @param string[] $package shipping package data, unused
	 * @param WC_Shipping_Method|bool $method the shipping method instance
	 * @return bool local pickup shipping availability
	 */
	public function availability( $available, $package, $method = false ) {
		if ( ! $method ) {
			wc_edostavka_shipping()->log( sprintf( 'Невозможно модифицировать метод доставки "Самовывоз" так как метод не определён. Backtrace: %s', wc_print_r( wp_debug_backtrace_summary(), true ) ) );
		}

		// ensure we're looking at a local pickup shipping rate assigned to a zone (instance ID can't be 0)
		if ( $method instanceof WC_Shipping_Local_Pickup && $method->get_instance_id() > 0 ) {

			$allowed_cities = $method->get_instance_option( 'allowed_cities', array() );

			if( ! empty( $allowed_cities ) && isset( $package['edostavka_customer_location'] ) ) {

				$customer_location = $package['edostavka_customer_location'];

				if( ! in_array( $customer_location['city_code'], $allowed_cities ) ) {
					$available = false;
				}

			}
		}

		return $available;
	}

	public function refresh_fields( $args ) {

		if( $disabled_fields = $this->get_fields_to_disable() ) {

			foreach ( $disabled_fields as $field ) {

				if( current_action() !== sprintf( 'edostavka_update_order_review_%s_args', $field ) ) continue;

				$args['required']   = false;
				$args['class']      = array( 'hidden' );
			}
		}

		return $args;
	}

	public function default_address_fields( $fields ) {
		if( $disabled_fields = $this->get_fields_to_disable() ) {
			foreach ( $disabled_fields as $field ) {
				$field = 'address' == $field ? 'address_1' : $field;
				if( isset( $fields[ $field ] ) ) {
					$fields[ $field ]['required']   = false;
					$fields[ $field ]['class']      = array( 'hidden' );
				}
			}
		}
		return $fields;
	}

	private function get_fields_to_disable() {
		if( WC()->session ) {
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods', array() );
			foreach ( $chosen_methods as $chosen_method ) {

				if( ! $chosen_method || ! Woodev_Helper::str_starts_with( $chosen_method, 'local_pickup' ) ) continue;

				list( $method_id, $method_instance ) = explode( ':', $chosen_method );

				$settings = get_option( sprintf(
					'woocommerce_%s_%s_settings',
					$method_id,
					$method_instance
				), array() );

				if( ! empty( $settings['disabled_fields'] ) ) {
					return $settings['disabled_fields'];
				}
			}
		}

		return false;
	}

	public function method_supports( $is_support, $feature, $method ) {
		if( $method instanceof WC_Shipping_Local_Pickup && 'instance-settings-modal' == wc_strtolower( $feature ) ) {
			return false;
		}

		return $is_support;
	}

	/**
	 * Helper to determine if we're on the shipping settings pages.
	 */
	private function is_shipping_settings() {

		$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$current_tab  = isset( $_GET['tab'] )  ? $_GET['tab']  : '';
		$instance_id = isset( $_GET['instance_id'] ) ? absint( $_GET['instance_id'] ) : 0;

		return 'wc-settings' === $current_page && 'shipping' === $current_tab && $instance_id > 0;
	}
}

return new WC_Edostavka_Local_Pickup_Shipping_Modifier;
