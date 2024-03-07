<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Checkout {

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), PHP_INT_MAX );
		add_filter( 'woocommerce_default_address_fields', array( $this, 'default_address_fields' ), 15 );
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'shipping_rate_additional_information' ) );

		add_action( 'woocommerce_review_order_after_shipping', array( $this, 'add_delivery_points_button' ) );
		add_action( 'wp_footer', array( $this, 'add_map_template' ) );

		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout' ), 10, 2 );

		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'update_order_review_fragments' ) );
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'localisation_address_formats' ) );

		add_action( 'woocommerce_checkout_before_customer_details', array(
			$this,
			'add_notice_before_checkout_filed'
		) );

		add_action( 'woocommerce_checkout_create_order', array( $this, 'checkout_create_order' ), 10, 2 );
		add_filter( 'woocommerce_checkout_posted_data', array( $this, 'checkout_posted_data' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'add_details_after_order_table' ) );
		add_action( 'wc_edostavka_after_details_order_table', array( $this, 'add_delivery_order_statuses' ) );

		add_action( 'woocommerce_created_customer', array( $this, 'created_customer' ) );

		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'cart_shipping_packages' ), 15 );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'get_available_payment_gateways' ) );
	}

	/**
	 * @throws Exception
	 */
	public function enqueue_scripts() {
		if ( wc_edostavka_is_admin_scope() ) {
			return;
		}

		$map_lang                   = ( get_locale() && in_array( get_locale(), array(
				'ru_RU',
				'en_US',
				'en_RU',
				'ru_UA',
				'uk_UA',
				'tr_TR'
			), true ) ) ? get_locale() : 'ru_RU';
		$ajax_url                   = admin_url( 'admin-ajax.php', 'relative' );
		$integration_handler        = wc_edostavka_shipping()->get_integration_handler();
		$enable_dropdown_city_field = $integration_handler->get_option( 'enable_dropdown_city_field', 'enable' );

		$map_api_url = add_query_arg( array(
			'load'   => 'package.standard',
			'lang'   => $map_lang,
			'ns'     => 'WCEdostavkaMaps',
			'apikey' => apply_filters( 'wc_edostavka_yandex_map_apikey', '38f393c8-f1fa-4b1a-a356-b8d9752ff229' )
		), 'https://api-maps.yandex.ru/2.1/' );

		wp_register_style( 'jquery-confirm', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css', array(), '3.3.2' );
		wp_register_style( 'wc-edostavka-checkout', wc_edostavka_shipping()->get_plugin_url() . '/assets/css/frontend/checkout.css', array( 'jquery-confirm' ), WC_CDEK_SHIPPING_VERSION );
		wp_register_style( 'jquery-suggestions', '//cdn.jsdelivr.net/npm/suggestions-jquery@22.6.0/dist/css/suggestions.min.css', array(), '22.6.0' );

		if ( ! wp_script_is( 'selectWoo', 'registered' ) ) {
			wp_register_script( 'selectWoo', plugins_url( 'assets/js/selectWoo/selectWoo.full.js', WC_PLUGIN_FILE ), array( 'jquery' ), '1.0.6', true );
		}

		if ( ! wp_script_is( 'wc-backbone-modal', 'registered' ) ) {
			wp_register_script( 'wc-backbone-modal', plugins_url( 'assets/js/admin/backbone-modal.js', WC_PLUGIN_FILE ), array(
				'underscore',
				'backbone',
				'wp-util'
			), WC_VERSION, true );
		}

		wp_register_script( 'edostavka-yandex-map', $map_api_url, array(), '2.1', true );
		wp_register_script( 'jquery-confirm', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js', array( 'jquery' ), '3.3.2' );
		wp_register_script( 'jquery-suggestions', '//cdn.jsdelivr.net/npm/suggestions-jquery@22.6.0/dist/js/jquery.suggestions.min.js', array( 'jquery' ), '22.6.0', true );
		wp_register_script( 'edostavka-suggestions-plugin', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/suggestions-plugin.js', array( 'jquery-suggestions' ), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'wc-edostavka-city-select', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/city-select.js', array(
			'jquery',
			'selectWoo'
		), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'wc-edostavka-checkout-city-select', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/checkout-city-select.js', array(
			'wc-edostavka-city-select',
			'jquery-blockui'
		), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'edostavka-address-autocomplete', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/address-autocomplete.js', array(
			'jquery',
			'jquery-suggestions'
		), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'edostavka-fields-autocomplete', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/fields-autocomplete.js', array(
			'edostavka-suggestions-plugin'
		), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'woodev-yandex-map-plugin', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/woodev-yandex-map-plugin.js', array(
			'edostavka-yandex-map',
			'underscore',
			'backbone',
			'wp-util',
			'jquery-blockui',
			'jquery-confirm'
		), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'edostavka_map_widget', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/widget-map.js', array(
			'woodev-yandex-map-plugin',
			'wc-backbone-modal'
		), WC_CDEK_SHIPPING_VERSION, true );
		wp_register_script( 'wc-edostavka-checkout', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/frontend/checkout.js', array( 'jquery' ), WC_CDEK_SHIPPING_VERSION, true );

		$customer_default_location = wc_get_customer_default_location();

		wp_localize_script( 'wc-edostavka-checkout-city-select', 'edostavka_checkout_params', apply_filters( 'wc_edostavka_checkout_params', array(
			'ajax_url'             => $ajax_url,
			'wc_ajax_url'          => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'customer_country'     => WC()->customer->get_billing_country(),
			'default_country'      => $customer_default_location['country'],
			'default_city'         => WC()->customer->get_billing_city(),
			'customer_id'          => WC()->customer->get_id(),
			'is_checkout'          => is_checkout(),
			'is_cart'              => is_cart(),
			'is_edit_address'      => is_wc_endpoint_url( 'edit-address' ),
			'allowed_zone'         => wc_edostavka_get_allowed_zone_locations(),
			'enable_custom_city'   => wc_string_to_bool( $integration_handler->get_option( 'enable_custom_city' ) ),
			'enable_dropdown_city' => $integration_handler->get_option( 'enable_dropdown_city_field', 'enable' ),
			'clean_address_field'  => wc_string_to_bool( $integration_handler->get_option( 'clean_address_field', 'yes' ) ),
			'customer_location'    => wc_edostavka_shipping()->get_customer_handler()->get_location(),
			'i18n_strings'         => array(
				'search_control_placeholder' => __( 'Enter text for find pick-up point', 'woocommerce-edostavka' ),
				'select_city'                => __( 'Select city', 'woocommerce-edostavka' ),
				'enter_city_name'            => __( 'Enter name of city', 'woocommerce-edostavka' ),
				'already_selected'           => __( 'You already selected this city', 'woocommerce-edostavka' )
			)
		) ) );

		$points_url_params = array(
			'action'     => 'edostavka_get_deliverypoints',
			'lang'       => wc_edostavka_get_locale(),
			'is_handout' => true,
			'weight_max' => round( $this->get_cart_contents_weight() )
		);

		wp_localize_script( 'edostavka_map_widget', 'edostavka_widget_map_params', apply_filters( 'wc_edostavka_widget_map_params', array(
			'points_url'              => add_query_arg( $points_url_params, esc_url( $ajax_url ) ),
			'set_delivery_point_ajax' => add_query_arg( array( 'action' => 'edostavka_set_delivery_point' ), esc_url( $ajax_url ) ),
			'postamat_icon'           => wc_edostavka_shipping()->get_plugin_url() . '/assets/img/pinPostamat.svg',
			'pvz_icon'                => wc_edostavka_shipping()->get_plugin_url() . '/assets/img/pinPVZ.svg',
			'enable_fill_postcode'    => $integration_handler->get_option( 'enable_fill_postcode_filed', 'no' ),
			'show_search_field'       => $integration_handler->get_option( 'show_search_field_on_map', 'no' ),
			'contents_dimensions'     => $this->get_cart_contents_dimensions(),
			'i18n_strings'            => array(
				'search_control_placeholder' => __( 'Enter text for find pick-up point', 'woocommerce-edostavka' )
			)
		) ) );

		wp_localize_script( 'wc-edostavka-checkout', 'edostavka_common_checkout_params', array(
			'available_fee_payments' => wc_edostavka_has_fee_payments()
		) );

		if ( is_checkout() ) {
			wp_enqueue_script( 'wc-edostavka-checkout' );
			wp_enqueue_script( 'edostavka_map_widget' );

			if ( 'inline-method' == $integration_handler->get_option( 'map_button_position', 'under-methods' ) ) {
				ob_start();
				wc_edostavka_shipping()->load_template( 'views/delivery-map-styles.php' );
				$button_css = ob_get_clean();
				wp_add_inline_style( 'wc-edostavka-checkout', $button_css );
			}

		}

		if ( is_cart() || is_checkout() || is_wc_endpoint_url( 'edit-address' ) ) {
			wp_enqueue_style( 'wc-edostavka-checkout' );

			if ( 'none' !== $enable_dropdown_city_field ) {
				wp_enqueue_script( 'wc-edostavka-checkout-city-select' );
			}
		}

		if ( is_checkout() || is_wc_endpoint_url( 'edit-address' ) ) {

			$dadata_available           = wc_edostavka_is_dadata_available( true );
			$enable_suggestions_state   = $integration_handler->get_option( 'enable_suggestions_state', 'no' );
			$enable_suggestions_city    = $integration_handler->get_option( 'enable_suggestions_city', 'no' );
			$enable_suggestions_address = $integration_handler->get_option( 'enable_suggestions_address', 'no' );
			$reload_checkout_fields     = $integration_handler->get_option( 'reload_checkout_fields', 'no' );
			$fill_postcode_field        = $integration_handler->get_option( 'fill_postcode_field', 'no' );
			$enable_detect_geolocation  = $integration_handler->get_option( 'enable_detect_customer_location', 'no' );

			if ( $dadata_available ) {

				if ( 'none' !== $enable_dropdown_city_field && wc_string_to_bool( $enable_suggestions_address ) ) {

					wp_enqueue_style( 'jquery-suggestions' );
					wp_enqueue_script( 'edostavka-address-autocomplete' );
					wp_localize_script( 'edostavka-address-autocomplete', 'edostavka_address_autocomplete_params', array(
						'ajax_url'               => $ajax_url,
						'token'                  => $integration_handler->get_option( 'dadata_token' ),
						'language'               => wc_edostavka_get_locale() === 'rus' ? 'ru' : 'en',
						'reload_checkout_fields' => wc_string_to_bool( $reload_checkout_fields ),
						'fill_postcode_field'    => wc_string_to_bool( $fill_postcode_field ),
						'detect_location'        => wc_string_to_bool( $enable_detect_geolocation ),
						'i18n_strings'           => array(
							'hint'    => __( 'Choose a suggestion or continue typing', 'woocommerce-edostavka' ), //TEXT: Выберите вариант или продолжите ввод
							'no_hint' => __( 'Unknown address', 'woocommerce-edostavka' ) //TEXT: Неизвестный адрес
						)
					) );

				} elseif ( 'none' == $enable_dropdown_city_field && in_array( true, array(
						wc_string_to_bool( $enable_suggestions_state ),
						wc_string_to_bool( $enable_suggestions_city ),
						wc_string_to_bool( $enable_suggestions_address )
					), true ) ) {

					wp_enqueue_style( 'jquery-suggestions' );
					wp_enqueue_script( 'edostavka-fields-autocomplete' );

					wp_localize_script( 'edostavka-fields-autocomplete', 'edostavka_fields_autocomplete_params', array(
						'ajax_url'                   => admin_url( 'admin-ajax.php', 'relative' ),
						'wc_ajax_url'                => WC_AJAX::get_endpoint( '%%endpoint%%' ),
						'requires_address'           => wc_string_to_bool( get_option( 'woocommerce_shipping_cost_requires_address' ) ),
						'enable_suggestions_state'   => $enable_suggestions_state,
						'enable_suggestions_city'    => $enable_suggestions_city,
						'enable_suggestions_address' => $enable_suggestions_address,
						'reload_checkout_fields'     => $reload_checkout_fields,
						'fill_postcode_field'        => $fill_postcode_field,
						'enable_detect_geolocation'  => wc_string_to_bool( $enable_detect_geolocation ) && empty( $integration_handler->get_option( 'customer_default_city', '' ) )
					) );

					wp_localize_script( 'edostavka-suggestions-plugin', 'wc_edostavka_suggestions_plugin_params', apply_filters( 'wc_edostavka_suggestions_plugin_default_params', array(
						'geoLocation'    => false,
						'token'          => $integration_handler->get_option( 'dadata_token' ),
						'countryISOCode' => $customer_default_location['country']
					) ) );
				}
			}
		}
	}

	public function default_address_fields( $fields ) {

		$start_priority = isset( $fields['country'], $fields['country']['priority'] ) ? $fields['country']['priority'] : 40; //Начальный приоритет полей

		//Сортировка полей по порядку формата РФ и удаление класса 'address-field' из полей state и city
		foreach ( array( 'state', 'city', 'address_1', 'address_2', 'postcode' ) as $field_key ) {

			if ( ! isset( $fields[ $field_key ] ) ) {
				continue;
			}

			$fields[ $field_key ]['priority'] = ++ $start_priority;

			if ( in_array( $field_key, array(
					'state',
					'city'
				), true ) && is_array( $fields[ $field_key ]['class'] ) ) {
				$fields[ $field_key ]['class'] = array_diff( $fields[ $field_key ]['class'], array( 'address-field' ) );
			}
		}

		//Если включена опция "Скрыть поле "Страна" и в списке стран только одна страна для доставки, то прячем поле "Страна".
		if ( wc_string_to_bool( wc_edostavka_shipping()->get_integration_handler()->get_option( 'hide_single_country' ) ) && 1 === sizeof( WC()->countries->get_allowed_countries() ) ) {
			$fields['country']['class'] = array( 'hidden' );
		}

		//Полчаем данные локации пользоватля и присваиваем полученные значения полям "Город" и "Регион".
		try {

			$fields['city']['default'] = wc_edostavka_shipping()->get_customer_handler()->get_city();

			if ( empty( $fields['state']['options'] ) ) {
				$fields['state']['default'] = wc_edostavka_shipping()->get_customer_handler()->get_region();
			}

		} catch ( Exception $e ) {
			wc_edostavka_shipping()->log( sprintf( 'Ошибка во время получения данных о локации пользователя в функции "%s": %s', __FUNCTION__, $e->getMessage() ) );
		}

		$method_instance        = wc_edostavka_get_chosen_method_instance(); //Получаем выбранный метод СДЭК
		$disable_state_field    = wc_edostavka_shipping()->get_integration_handler()->get_option( 'disable_state_field', 'always' );
		$disable_address_field  = wc_edostavka_shipping()->get_integration_handler()->get_option( 'disable_address_field', 'yes' );
		$disable_postcode_field = wc_edostavka_shipping()->get_integration_handler()->get_option( 'disable_postcode_field', 'none' );

		if ( isset( $fields['state'] ) && $disable_state_field == 'always' ) {
			if ( apply_filters( 'wc_edostavka_checkout_hidden_or_remove_state_field', false, $disable_state_field, $fields ) ) {
				$fields['state']['class']    = array( 'hidden' );
				$fields['state']['required'] = false;
				$fields['state']['type']     = 'text';
			} else {
				unset( $fields['state'] );
			}
		}

		if ( isset( $fields['postcode'] ) && $disable_postcode_field == 'always' ) {
			if ( apply_filters( 'wc_edostavka_checkout_hidden_or_remove_postcode_field', false, $disable_postcode_field, $fields ) ) {
				$fields['postcode']['required'] = false;
				$fields['postcode']['class']    = array( 'hidden' );
			} else {
				unset( $fields['postcode'] );
			}
		}

		if ( $method_instance ) {

			$tariff_id = $method_instance->get_option( 'tariff' );
			$tariff    = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $tariff_id );
			$is_door   = Woodev_Helper::str_ends_with( $tariff['type'], '_door' );

			if ( isset( $fields['state'] ) ) {

				$fields['state']['required'] = false;

				if ( 'none' !== $disable_state_field ) {
					$fields['state']['class'] = array( 'hidden' );
				}
			}

			if ( isset( $fields['address_1'] ) ) {

				$fields['address_1']['required'] = $is_door;

				if ( ! $is_door && wc_string_to_bool( $disable_address_field ) ) {
					$fields['address_1']['class'] = array( 'hidden' );
				}
			}

			if ( isset( $fields['address_2'] ) ) {

				if ( ! $is_door && wc_string_to_bool( $disable_address_field ) ) {
					$fields['address_2']['class'] = array( 'hidden' );
				}
			}

			if ( isset( $fields['postcode'] ) ) {

				$fields['postcode']['required'] = false;

				if ( 'none' !== $disable_postcode_field ) {
					$fields['postcode']['class'] = array( 'hidden' );
				}
			}

		}

		return $fields;
	}

	/**
	 * @param WC_Shipping_Rate $method
	 *
	 * @return void
	 * @throws Exception
	 */
	public function shipping_rate_additional_information( $method ) {

		if ( $method->get_method_id() !== wc_edostavka_shipping()->get_method_id() ) {
			return;
		}

		$method_instance         = WC_Shipping_Zones::get_shipping_method( $method->get_instance_id() );
		$meta_data               = $method->get_meta_data();
		$additional_info_strings = array();

		if ( wc_string_to_bool( $method_instance->get_option( 'show_delivery_time' ) ) && isset( $meta_data['edostavka_rate'] ) ) {

			$additional_time = $method_instance->get_option( 'additional_time' ) ? intval( $method_instance->get_option( 'additional_time' ) ) : 0;
			$period_min      = isset( $meta_data['edostavka_rate']['period_min'] ) ? intval( $meta_data['edostavka_rate']['period_min'] ) : 0;
			$period_max      = isset( $meta_data['edostavka_rate']['period_max'] ) ? intval( $meta_data['edostavka_rate']['period_max'] ) : 0;
			$period_min      = $period_min + $additional_time;
			$period_max      = $period_max + $additional_time;

			if ( $period_min || $period_max ) {

				$period = '';

				if ( $period_min !== $period_max ) {
					if ( $period_min > 0 ) {
						$period .= sprintf( _x( 'from %s', 'кол-во дней доставки (от)', 'woocommerce-edostavka' ), $period_min );
					}

					if ( $period_max > 0 && $period_max > $period_min ) {
						$period .= sprintf( _x( ' to %s', 'кол-во дней доставки (до)', 'woocommerce-edostavka' ), $period_max );
					}

				} else {
					$period = $period_min > 0 ? $period_min : ( max( $period_max, 0 ) );
				}

				$additional_info_strings[] = sprintf( '<p class="wc-edostavka-method-delivery-time"><strong>%s</strong>: %s %s</p>', __( 'Delivery days count', 'woocommerce-edostavka' ), $period, _n( 'day', 'days', intval( $period ), 'woocommerce-edostavka' ) );
			}
		}

		$instruction_raw_text = apply_filters( 'wc_edostavka_method_instruction_text', $method_instance->get_option( 'rate_instruction' ), $method, $method_instance, $this );

		if ( ! empty( $instruction_raw_text ) ) {
			$additional_info_strings[] = sprintf( '<p class="wc-edostavka-method-description">%s</p>', esc_textarea( $instruction_raw_text ) );
		}

		if ( is_checkout() && 'inline-method' == wc_edostavka_shipping()->get_integration_handler()->get_option( 'map_button_position', 'under-methods' ) ) {

			$tariff_id = $method_instance->get_option( 'tariff' );
			$tariff    = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $tariff_id );
			list( , $delivery_type ) = explode( '_', $tariff['type'] );

			if ( in_array( $delivery_type, array( 'stock', 'postamat' ), true ) ) {

				$point_type_text       = $delivery_type == 'stock' ? __( 'Pick-up', 'woocommerce-edostavka' ) : __( 'Postamat', 'woocommerce-edostavka' );
				$customer_handler      = wc_edostavka_shipping()->get_customer_handler();
				$chosen_delivery_point = wc_edostavka_get_delivery_point_data( $customer_handler->get_city_code(), $delivery_type == 'stock' ? 'pvz' : 'postamat' );
				$is_chosen             = ( boolean ) $chosen_delivery_point && ! empty( $chosen_delivery_point['location'] );

				$delivery_button = '<div class="wc-edostavka-method-button">';

				if ( $is_chosen ) {
					$delivery_button .= sprintf( '<p>%s: <strong>%s</strong></p>', sprintf( __( 'Chosen %s point', 'woocommerce-edostavka' ), strtolower( $point_type_text ) ), $chosen_delivery_point['location']['address'] );
				}

				$delivery_button .= wc_edostavka_generate_delivery_point_button( array(
					'delivery_type'   => $delivery_type,
					'city_id'         => $customer_handler->get_city_code(),
					'chose_btn_text'  => sprintf( __( 'Choose %s point', 'woocommerce-edostavka' ), $point_type_text ),
					'chosen_btn_text' => sprintf( __( 'Choose another %s point', 'woocommerce-edostavka' ), strtolower( $point_type_text ) ),
					'is_chosen'       => $is_chosen,
					'use_css'         => false
				) );

				$delivery_button .= '</div>';

				$additional_info_strings[] = $delivery_button;
			}
		}

		$additional_info_strings = apply_filters( 'wc_edostavka_method_additional_info_strings', $additional_info_strings, $method, $method_instance, $this );

		if ( array_filter( $additional_info_strings ) ) {
			printf( '<div class="wc-edostavka-method-additional-info">%s</div>', implode( '', $additional_info_strings ) );
		}
	}

	public function update_order_review_fragments( $fragments ) {

		$checkout_fields = WC()->checkout()->get_checkout_fields( 'billing' );
		$chosen_rate     = wc_edostavka_chosen_rate_is_edostavka_shipping( true );
		$delivery_type   = $method_instance = false;

		if ( $chosen_rate ) {
			/** @var WD_Edostavka_Shipping $method_instance */
			$method_instance = WC_Shipping_Zones::get_shipping_method( $chosen_rate );
			$tariff_id       = $method_instance->get_option( 'tariff' );
			$tariff          = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $tariff_id );
			$delivery_type   = $tariff['type'];
		}

		$billing_address_args = $billing_postcode_args = $billing_state_args = array();

		if ( isset( $checkout_fields['billing_address_1'] ) ) {
			$billing_address_args            = $checkout_fields['billing_address_1'];
			$billing_address_args['return']  = true;
			$billing_address_args['default'] = WC()->customer->get_billing_address_1();
		}

		if ( isset( $checkout_fields['billing_postcode'] ) ) {
			$billing_postcode_args            = $checkout_fields['billing_postcode'];
			$billing_postcode_args['return']  = true;
			$billing_postcode_args['default'] = WC()->customer->get_billing_postcode();
		}

		if ( isset( $checkout_fields['billing_state'] ) ) {
			$billing_state_args            = $checkout_fields['billing_state'];
			$billing_state_args['return']  = true;
			$billing_state_args['default'] = WC()->customer->get_billing_state();
		}

		if ( $delivery_type && $method_instance ) {

			$disable_state_field    = $method_instance->get_option( 'disable_state_field', 'always' );
			$disable_address_field  = $method_instance->get_option( 'disable_address_field', 'yes' );
			$disable_postcode_field = $method_instance->get_option( 'disable_postcode_field', 'none' );
			$is_door                = Woodev_Helper::str_ends_with( $delivery_type, '_door' );

			if ( $billing_state_args ) {
				$billing_state_args['required'] = false;
				if ( 'none' !== $disable_state_field ) {
					$billing_state_args['class'] = array( 'hidden' );
				}
			}

			if ( $billing_address_args && ! $is_door ) {
				$billing_address_args['required'] = false;
				if ( wc_string_to_bool( $disable_address_field ) ) {
					$billing_address_args['class'] = array( 'hidden' );
				}
			}

			if ( $billing_postcode_args ) {
				$billing_postcode_args['required'] = false;
				if ( 'none' !== $disable_postcode_field ) {
					$billing_postcode_args['class'] = array( 'hidden' );
				}
			}

		}

		if ( $billing_state_args ) {
			$billing_state_args                = apply_filters( 'edostavka_update_order_review_state_args', $billing_state_args, $chosen_rate );
			$fragments['#billing_state_field'] = woocommerce_form_field( 'billing_state', $billing_state_args );
		}

		if ( $billing_address_args ) {
			$billing_address_args                  = apply_filters( 'edostavka_update_order_review_address_args', $billing_address_args, $chosen_rate );
			$fragments['#billing_address_1_field'] = woocommerce_form_field( 'billing_address_1', $billing_address_args );
		}

		if ( $billing_postcode_args ) {
			$billing_postcode_args                = apply_filters( 'edostavka_update_order_review_postcode_args', $billing_postcode_args, $chosen_rate );
			$fragments['#billing_postcode_field'] = woocommerce_form_field( 'billing_postcode', $billing_postcode_args );
		}

		return $fragments;
	}

	/**
	 * @throws Exception
	 */
	public function add_delivery_points_button() {

		if ( wc_edostavka_shipping()->get_integration_handler()->get_option( 'map_button_position', 'under-methods' ) !== 'under-methods' ) {
			return;
		}

		$chosen_rate = wc_edostavka_chosen_rate_is_edostavka_shipping( true );

		if ( is_checkout() && $chosen_rate ) {
			$method_instance = WC_Shipping_Zones::get_shipping_method( $chosen_rate );
			$tariff_id       = $method_instance->get_option( 'tariff' );
			$tariff          = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $tariff_id );
			list( , $delivery_type ) = explode( '_', $tariff['type'] );

			if ( in_array( $delivery_type, array( 'stock', 'postamat' ), true ) ) {

				$out                   = __return_empty_string();
				$point_type_name       = $delivery_type == 'stock' ? __( 'Pick-up', 'woocommerce-edostavka' ) : __( 'Postamat', 'woocommerce-edostavka' );
				$customer_handler      = wc_edostavka_shipping()->get_customer_handler();
				$chosen_delivery_point = wc_edostavka_get_delivery_point_data( $customer_handler->get_city_code(), $delivery_type == 'stock' ? 'pvz' : 'postamat' );
				$is_chosen             = ( boolean ) $chosen_delivery_point && ! empty( $chosen_delivery_point['location'] );

				if ( $is_chosen ) {
					$out .= sprintf( '<p>%s: <strong>%s</strong></p>', sprintf( __( 'Chosen %s point', 'woocommerce-edostavka' ), strtolower( $point_type_name ) ), $chosen_delivery_point['location']['address'] );
				}

				$out .= wc_edostavka_generate_delivery_point_button( array(
					'delivery_type'   => $delivery_type,
					'city_id'         => $customer_handler->get_city_code(),
					'chose_btn_text'  => sprintf( __( 'Choose %s point', 'woocommerce-edostavka' ), $point_type_name ),
					'chosen_btn_text' => sprintf( __( 'Choose another %s point', 'woocommerce-edostavka' ), strtolower( $point_type_name ) ),
					'is_chosen'       => $is_chosen
				) );

				$button_html_wrapper = apply_filters( 'woocommerce_edostavka_cart_delivery_points_template', sprintf( '<tr class="cart-delivery-points"><th>%s: </th><td>%s</td></tr>', __( 'Pick-up point of CDEK', 'woocommerce-edostavka' ), $out ), $out );
				echo $button_html_wrapper;
			}
		}
	}

	public function add_map_template() {
		if ( is_checkout() ) {
			wc_edostavka_shipping()->load_template( 'views/html-modal-map.php' );
		}
	}

	/**
	 * @param array $data Posted data.
	 *
	 * @return array of data.
	 * @throws Exception
	 */
	public function checkout_posted_data( $data ) {

		if ( $data['shipping_method'] && is_array( $data['shipping_method'] ) ) {

			foreach ( ( array ) $data['shipping_method'] as $key => $shipping_method ) {

				if ( ! Woodev_Helper::str_starts_with( $shipping_method, wc_edostavka_shipping()->get_method_id() ) ) {
					continue;
				}

				list( $method_name, $instance, $code ) = explode( ':', $shipping_method );

				$data['edostavka_shipping'][ $key ] = array(
					'tariff_data'     => WC_Edostavka_Tariffs_Data::get_tariff_by_code( $code ),
					'method_instance' => WC_Shipping_Zones::get_shipping_method( $instance )
				);
			}
		}

		return $data;
	}

	/**
	 * @param array    $data   An array of posted data {@see WC_Checkout::get_posted_data()}
	 * @param WP_Error $errors Validation errors
	 *
	 * @return void
	 * @throws Exception
	 */
	public function validate_checkout( $data, WP_Error $errors ) {

		$required_field_errors = $errors->get_error_data( 'required-field' );

		if ( ! empty( $required_field_errors ) ) {
			return;
		}

		if ( WC()->cart && WC()->cart->needs_shipping() ) {

			if ( isset( $data['edostavka_shipping'] ) && is_array( $data['edostavka_shipping'] ) ) {

				$customer_location = wc_edostavka_get_customer_location();

				foreach ( ( array ) $data['edostavka_shipping'] as $edostavka_shipping ) {

					list( , $delivery_type ) = explode( '_', $edostavka_shipping['tariff_data']['type'] );

					if ( ! in_array( $delivery_type, array( 'stock', 'postamat' ), true ) ) {
						continue;
					}

					$chosen_delivery_point = wc_edostavka_get_delivery_point_data( $customer_location['city_code'], $delivery_type == 'stock' ? 'pvz' : 'postamat' );
					$point_type_name       = $delivery_type == 'stock' ? __( 'Pick-up', 'woocommerce-edostavka' ) : __( 'Postamat', 'woocommerce-edostavka' );

					if ( ! $chosen_delivery_point ) {
						$errors->add( 'shipping', sprintf( __( 'To continue with your order, please <strong>choose %s point</strong> or another shipping method.', 'woocommerce-edostavka' ), strtolower( $point_type_name ) ) );
					} else {

						$instruction_text = __( sprintf( 'Please choose another %s point either reduce the number of items in your basket or choosing another shipping method.', strtolower( $point_type_name ) ), 'woocommerce-edostavka' );

						if ( $chosen_delivery_point['weight_max'] && ( $this->get_cart_contents_weight() > $chosen_delivery_point['weight_max'] ) ) {
							$errors->add( 'shipping', sprintf( __( 'The total weight of your order is %d kg more than the maximum allowed weight is %d kg for your chosen %s point. %s', 'woocommerce-edostavka' ), $this->get_cart_contents_weight(), $chosen_delivery_point['weight_max'], strtolower( $point_type_name ), $instruction_text ) );
						}

						if ( isset( $chosen_delivery_point['dimensions'] ) ) {

							$max_dimensions      = max( $chosen_delivery_point['dimensions'] );
							$total_volume        = $max_dimensions['width'] * $max_dimensions['height'] * $max_dimensions['depth'];
							$max_dimensions      = max( $max_dimensions );
							$contents_dimensions = $this->get_cart_contents_dimensions();

							if ( $contents_dimensions['max_size'] > $max_dimensions || ( $max_dimensions > $contents_dimensions['max_size'] && $contents_dimensions['volume'] > $total_volume ) ) {
								$errors->add( 'shipping', sprintf( __( 'The total volume of your order exceed the maximum allowed volume to chosen %s. %s', 'woocommerce-edostavka' ), strtolower( $point_type_name ), $instruction_text ) );
							}
						}

						if ( $data['payment_method'] === 'cod' && 'false' === $chosen_delivery_point['allowed_cod'] ) {
							$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
							$payment_method     = $available_gateways[ $data['payment_method'] ];
							$errors->add( 'shipping', sprintf( __( 'The payment method <strong>%s</strong> disallowed for chosen %s point. To continue with your order, please choosing another %s point eather payment method or shipping method.', 'woocommerce-edostavka' ), $payment_method->get_title(), strtolower( $point_type_name ), strtolower( $point_type_name ) ) );
						}

					}
				}
			}
		}
	}

	public function localisation_address_formats( $address_formats ) {

		if ( ! isset( $address_formats['RU'] ) ) {
			$address_formats['RU'] = "{company}\n{name}\n{country}\n{state}\n{postcode}, {city}, {address_1}, {address_2}";
		}

		return $address_formats;
	}

	public function add_notice_before_checkout_filed() {
		if ( current_user_can( 'manage_options' ) ) {
			$currency             = wc_edostavka_shipping()->get_integration_handler()->get_option( 'currency', 'default' );
			$woocommerce_currency = get_woocommerce_currency();

			if ( 'default' !== $currency && $woocommerce_currency !== $currency ) {

				$all_currencies = get_woocommerce_currencies();

				wc_print_notice( sprintf(
					implode( ' ', array(
						__( '<strong>Attention!</strong> Current currency of shop <strong>%s (%s)</strong> not match to setted currency for calculate shipping cost through CDEK', 'woocommerce-edostavka' ),
						__( 'Cost of CDEK delivery will be calculated in currency - <strong>%s (%s)</strong>', 'woocommerce-edostavka' ),
						__( 'You can change this value on <a href="%s">plugin settings %s</a>.', 'woocommerce-edostavka' )
					) ),
					$all_currencies[ $woocommerce_currency ],
					get_woocommerce_currency_symbol( $woocommerce_currency ),
					$all_currencies[ $currency ],
					get_woocommerce_currency_symbol( $currency ),
					esc_url( wc_edostavka_shipping()->get_settings_url( wc_edostavka_shipping()->get_method_id() ) ),
					esc_attr( wc_edostavka_shipping()->get_plugin_name() )
				), 'notice' );
			}

			if ( ! wc_edostavka_shipping()->get_license_instance()->is_active() ) {
				wc_print_notice( sprintf(
					implode( ' ', array(
						__( 'Plugin %s disabled, because license key is not entered, entered incorrect, duration is expired or license activated for another domain', 'woocommerce-edostavka' ),
						__( 'To fix it, go to <a href="%s">Woodev licenses page</a> and enter your actual license key for current web-site.', 'woocommerce-edostavka' )
					) ),
					esc_attr( wc_edostavka_shipping()->get_plugin_name() ),
					esc_url( wc_edostavka_shipping()->get_license_instance()->get_license_settings_url() )
				), 'error' );
			}
		}
	}

	public function get_cart_contents_weight() {
		$weight = 0;

		if ( WC()->cart && WC()->cart->get_cart_contents_weight() ) {
			$cart_weight = WC()->cart->get_cart_contents_weight();
			if ( $cart_weight > 0 ) {
				$weight = wc_get_weight( $cart_weight, 'kg' );
			} else {
				$default_weight = wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_weight', 100 );
				$weight         = wc_get_weight( $default_weight * WC()->cart->get_cart_contents_count(), 'kg', 'g' );
			}
		}

		return $weight;
	}

	public function get_cart_contents_dimensions() {

		$dimensions = array();

		if ( WC()->cart && WC()->cart->get_cart() ) {

			$default_height = wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_height' );
			$default_width  = wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_width' );
			$default_length = wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_length' );
			$volume         = 0;
			$max_values     = array();
			$packing_method = wc_edostavka_shipping()->get_integration_handler()->get_option( 'packing_method', 'per_item' );
			$packing_method = in_array( $packing_method, array( 'single_box', 'box_packing' ), true ) ? $packing_method : 'single_box';
			$packer         = wc_edostavka_get_package_box_handler( $packing_method );

			foreach ( ( array ) WC()->cart->get_cart() as $values ) {

				$product = $values['data'];

				if ( $product && $product->needs_shipping() ) {

					$product_height = $product->get_height() > 0 ? wc_get_dimension( $product->get_height(), 'cm' ) : $default_height;
					$product_width  = $product->get_width() > 0 ? wc_get_dimension( $product->get_width(), 'cm' ) : $default_width;
					$product_length = $product->get_length() > 0 ? wc_get_dimension( $product->get_length(), 'cm' ) : $default_length;

					$product_height = ceil( $product_height );
					$product_width  = ceil( $product_width );
					$product_length = ceil( $product_length );

					for ( $i = 0; $i < absint( $values['quantity'] ); $i ++ ) {

						$new_item = new Woodev_Packer_Item_Implementation( $product_length, $product_width, $product_height );

						$new_item->set_product( $product );

						$packer->add_item( $new_item );
					}
				}
			}

			$packaged = $packer->get_packages();

			foreach ( $packaged['packages'] as $packed ) {
				$box = $packed['box'];

				$max_values[] = max( array( $box['height'], $box['width'], $box['length'] ) );

				$volume += ( $box['height'] * $box['width'] * $box['length'] );
			}

			$dimensions = array(
				'volume'   => $volume,
				'max_size' => ! empty( $max_values ) ? floatval( max( $max_values ) ) : 0
			);
		}

		return $dimensions;
	}

	/**
	 * @param WC_Order $order
	 * @param array    $data Posted data.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function checkout_create_order( $order, $data ) {

		if ( ! is_a( $order, 'WC_Order' ) ) {
			throw new Exception( __( 'The $order variable is not an object of the WC_Order class.', 'woocommerce-edostavka' ) );
		}

		$customer_location = wc_edostavka_get_customer_location();

		if ( isset( $data['edostavka_shipping'] ) ) {

			$order->update_meta_data( '_wc_edostavka_shipping', $data['edostavka_shipping'] );
			$order->update_meta_data( '_wc_edostavka_status', 'NEW' );

			foreach ( ( array ) $data['edostavka_shipping'] as $key => $edostavka_shipping ) {

				list( , $delivery_type ) = explode( '_', $edostavka_shipping['tariff_data']['type'] );

				if ( ! in_array( $delivery_type, array( 'stock', 'postamat' ), true ) ) {
					continue;
				}

				$chosen_delivery_point = wc_edostavka_get_delivery_point_data( $customer_location['city_code'], $delivery_type == 'stock' ? 'pvz' : 'postamat' );
				$point_data[ $key ]    = $chosen_delivery_point;

				$order->update_meta_data( '_wc_edostavka_chosen_delivery_point', $point_data );

				$order->set_billing_city( $chosen_delivery_point['location']['city'] );
				$order->set_shipping_city( $chosen_delivery_point['location']['city'] );
				$order->set_billing_address_1( $chosen_delivery_point['location']['address'] );
				$order->set_shipping_address_1( $chosen_delivery_point['location']['address'] );
				$order->set_billing_postcode( $chosen_delivery_point['location']['postal_code'] );
				$order->set_shipping_postcode( $chosen_delivery_point['location']['postal_code'] );
			}
		}

		if ( $customer_location ) {

			$order->update_meta_data( '_wc_edostavka_customer_location', $customer_location );

			foreach ( array( 'billing', 'shipping' ) as $type ) {
				$checkout_fields = WC()->checkout()->get_checkout_fields( $type );
				$state_field     = $type . '_state';

				if ( isset( $checkout_fields[ $state_field ], $checkout_fields[ $state_field ]['options'] ) && ! empty( $checkout_fields[ $state_field ]['options'] ) ) {
					continue;
				}

				if ( is_callable( array(
						$order,
						"get_{$state_field}"
					) ) && empty( $order->{"get_{$state_field}"}( 'edit' ) ) ) {
					$order->{"set_{$state_field}"}( $customer_location['region'] );
				}
			}
		}

	}

	public function add_details_after_order_table( $order ) {

		$order = new WC_Edostavka_Shipping_Order( $order );

		if ( $order->has_edostavka_shipping() ) {

			$chosen_delivery_point = $order->get_order_meta( 'chosen_delivery_point' );

			if ( $chosen_delivery_point ) {

				foreach ( ( array ) $chosen_delivery_point as $point ) {
					if ( ! in_array( $point['type'], array( 'PVZ', 'POSTAMAT' ), true ) ) {
						continue;
					}

					wc_edostavka_shipping()->load_template( 'views/html-order-delivery-point.php', array(
						'title' => sprintf( __( 'Details of the chosen %s', 'woocommerce-edostavka' ), $point['type'] == 'PVZ' ? _x( 'pick-up point', 'Предложный падеж', 'woocommerce-edostavka' ) : _x( 'postamat', 'Предложный падеж', 'woocommerce-edostavka' ) ),
						'point' => $point
					) );
				}

			}
		}

		do_action( 'wc_edostavka_after_details_order_table', $order );

	}

	/**
	 * @param WC_Edostavka_Shipping_Order $order
	 *
	 * @return void
	 */
	public function add_delivery_order_statuses( $order ) {

		if ( $order->has_edostavka_shipping() ) {

			wc_edostavka_shipping()->load_template( 'views/html-order-delivery-status.php', array(
				'title'       => __( 'Delivery status of CDEK', 'woocommerce-edostavka' ),
				'statuses'    => $order->get_order_meta( 'history_statuses' ),
				'cdek_number' => wc_edostavka_get_tracking_code( $order )
			) );
		}
	}

	/**
	 * @param int $customer_id ID of customer
	 *
	 * @return void
	 * @throws Exception
	 */
	public function created_customer( $customer_id ) {

		$meta_name = 'wc_edostavka_customer_location';

		if ( $customer_id > 0 && WC()->session && isset( WC()->session->{$meta_name} ) ) {

			$session_data = WC()->session->get( $meta_name, array() );
			$customer     = new WC_Customer( $customer_id );

			$customer->update_meta_data( $meta_name, $session_data );
			$customer->save_meta_data();
		}
	}

	public function cart_shipping_packages( $packages ) {

		$new_packages = array();

		try {

			$customer_locations = wc_edostavka_shipping()->get_customer_handler();

			/**
			 * If are customer location data is not empty and customer billing city is empty or not equals to customer location city
			 * in this case, we will to set the data
			 */
			if (
				! empty( $customer_locations->get_city() ) &&
				(
					empty( WC()->customer->get_billing_city( 'edit' ) ) ||
					WC()->customer->get_billing_city( 'edit' ) !== $customer_locations->get_city()
				)
			) {
				WC()->customer->set_billing_city( $customer_locations->get_city() );
				WC()->customer->set_shipping_city( $customer_locations->get_city() );
			}

			/**
			 * If a customer county code is not empty and allowed countries have a customer county
			 * Set the customer billing country code
			 */
			if (
				! empty( $customer_locations->get_country_code() ) &&
				array_key_exists( $customer_locations->get_country_code(), WC()->countries->get_shipping_countries() ) &&
				! WC()->customer->get_billing_country( 'edit ' )
			) {
				WC()->customer->set_billing_country( $customer_locations->get_country_code() );
				WC()->customer->set_shipping_country( $customer_locations->get_country_code() );
			}

			/**
			 * If customer location region is not empty and
			 * not empty customer location country code and
			 * state of country is not exists or
			 * state of country is array and this array have customer location region
			 * Set the customer billing state
			 */
			if (
				! empty( $customer_locations->get_region() ) &&
				(
					! empty( $customer_locations->get_country_code( 'edit' ) ) &&
					(
						! WC()->countries->get_states( $customer_locations->get_country_code() ) ||
						(
							is_array( WC()->countries->get_states( $customer_locations->get_country_code() ) ) &&
							in_array( $customer_locations->get_region(), WC()->countries->get_states( $customer_locations->get_country_code() ) )
						)
					)
				)
			) {

				if (
					is_array( WC()->countries->get_states( $customer_locations->get_country_code() ) ) &&
					in_array( $customer_locations->get_region(), WC()->countries->get_states( $customer_locations->get_country_code() ) )
				) {
					$states    = WC()->countries->get_states( $customer_locations->get_country_code() );
					$state_key = array_search( wc_strtolower( $customer_locations->get_region() ), array_map( 'wc_strtolower', $states ) );
					if ( $state_key !== false ) {
						WC()->customer->set_billing_state( $state_key );
						WC()->customer->set_shipping_state( $state_key );
					}
				} else {
					WC()->customer->set_billing_state( $customer_locations->get_region() );
					WC()->customer->set_shipping_state( $customer_locations->get_region() );
				}

			}

			if ( ! empty( WC()->customer->get_changes() ) ) {
				WC()->customer->save();
			}

			foreach ( $packages as $index => $package ) {

				$new_packages[ $index ]                                = $package;
				$new_packages[ $index ]['edostavka_customer_location'] = $customer_locations->get_location();

				if ( wc_edostavka_has_fee_payments() ) {
					$new_packages[ $index ]['chosen_payment_method'] = WC()->session->get( 'chosen_payment_method' );
				}

			}

			return $new_packages;

		} catch ( Exception $e ) {
			wc_edostavka_shipping()->log( $e->getMessage() );
		}

		return $packages;
	}

	public function get_available_payment_gateways( $available_payments ) {

		if ( WC()->session ) {

			$chosen_methods = WC()->session->get( 'chosen_shipping_methods', array() );

			foreach ( $chosen_methods as $chosen_method ) {

				if ( ! $chosen_method || ! Woodev_Helper::str_starts_with( $chosen_method, wc_edostavka_shipping()->get_method_id() ) ) {
					continue;
				}

				list( $method_id, $method_instance ) = explode( ':', $chosen_method );

				$options = get_option( sprintf(
					'woocommerce_%s_%s_settings',
					$method_id,
					$method_instance
				), array() );

				if ( ! empty( $options ) && ! empty( $options['allowed_payments'] ) && is_array( $options['allowed_payments'] ) ) {
					$available_payments = array_intersect_key( $available_payments, array_flip( $options['allowed_payments'] ) );
					break;
				}
			}
		}

		return $available_payments;
	}
}
