<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping extends WC_Shipping_Method {

	private $locations_to = array();

	public function __construct( $instance_id = 0 ) {

		parent::__construct( $instance_id );

		$this->id                 = wc_edostavka_shipping()->get_method_id();
		$this->method_title       = 'СДЭК доставка';
		$this->method_description = '<div class="cdek-method-description">Метод <strong>СДЭК доставка</strong> позвоялет расчитать стоимость доставки через курьерскую службу <a href="https://www.cdek.ru/ru/" target="_blank">СДЭК</a>.</div>';

		$this->supports = array(
			'shipping-zones',
			'instance-settings'
		);

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_shipping_zone_shipping_methods', array( $this, 'zone_shipping_methods' ) );
	}

	public function admin_options() {

		if ( $this->instance_id ) {
			wp_enqueue_style( 'wc-edostavka-shipping-jquery-confirm', wc_edostavka_shipping()->get_framework_assets_url() . '/css/admin/jquery-confirm.min.css' );
			wp_enqueue_style( 'wc-edostavka-shipping-admin-confirm', wc_edostavka_shipping()->get_framework_assets_url() . '/css/admin/admin-confirm.css' );
			wp_enqueue_style( 'wc-edostavka-shipping-settings', wc_edostavka_shipping()->get_plugin_url() . '/assets/css/admin/admin-styles.css' );

			wp_register_script( 'wc-edostavka-shipping-jquery-confirm', wc_edostavka_shipping()->get_framework_assets_url() . '/js/admin/jquery.jquery-confirm.min.js', array( 'jquery' ), null, true );
			wp_enqueue_script( 'wc-edostavka-shipping-settings', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/admin/shipping-method.js', array(
				'jquery',
				'selectWoo',
				'wc-edostavka-shipping-jquery-confirm'
			), WC_CDEK_SHIPPING_VERSION, true );

			wp_localize_script( 'wc-edostavka-shipping-settings', 'wc_edostavka_shipping_settings_params', array(
				'ajax_url'     => admin_url( 'admin-ajax.php', 'relative' ),
				'country_code' => WC()->countries->get_base_country(),
				'locale'       => wc_edostavka_get_locale(),
				'saved_cities' => isset( $this->instance_settings['saved_cities'] ) ? $this->get_instance_option( 'saved_cities', array() ) : array()
			) );

			wc_edostavka_shipping()->load_template( 'views/html-admin-settings-shipping.php' );
		}

		parent::admin_options();
	}

	public function get_method_title() {

		$tariff_code = $this->get_option( 'tariff' );

		if ( $tariff_code && $used_tariff = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $tariff_code ) ) {
			return $used_tariff['name'];
		}

		return $this->method_title;
	}

	public function get_method_description() {

		$tariff_code = $this->get_option( 'tariff' );

		if ( $tariff_code && $used_tariff = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $tariff_code ) ) {
			ob_start();
			wc_edostavka_shipping()->load_template( 'views/html-shipping-method-description.php', $used_tariff );

			return ob_get_clean();
		}

		return $this->method_description;
	}

	public function init_form_fields() {

		if ( ! $this->instance_id ) {
			return;
		}


		$this->instance_form_fields = array(
			'general_settings'     => array(
				'title' => 'Основные настройки метода',
				'type'  => 'title'
			),
			'title'                => array(
				'title'       => 'Заголовок',
				'type'        => 'text',
				'description' => 'Этот заголовок будет отображатся на странице оформления заказа.',
				'desc_tip'    => true,
				'default'     => $this->method_title,
			),
			'tariff'               => array(
				'title'    => 'Тариф',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select wc-edostavka-tariff-select',
				'desc_tip' => 'Выберите нужный тариф для данного метода доставки.',
				'default'  => 137,
				'options'  => $this->get_tariffs_list()
			),
			'sender_city'          => array(
				'title'             => 'Город отправитель',
				'type'              => 'select',
				'class'             => 'wc-edostavka-sender-city-select',
				'desc_tip'          => 'Выберите город из которого будет производиться отгрузка товаров.',
				'placeholder'       => 'Выберите город отправитель',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите город отправитель'
				)
			),
			'delivery_point'       => array(
				'title'             => 'Пункт приёма заказов',
				'type'              => 'select',
				'class'             => 'wc-enhanced-select wc-edostavka-delivery-point-select',
				'desc_tip'          => 'Выберите пункт в который вы будете отгружать ваши заказы.',
				'placeholder'       => 'Выберите пункт отгрузки товаров',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите пункт отгрузки товаров'
				)
			),
			'dropoff_address'      => array(
				'title'       => 'Адрес вашего склада',
				'type'        => 'text',
				'desc_tip'    => 'Укажите адрес вашего склада откуда будет забирать товар курьер.',
				'placeholder' => 'Укажите адрес склада отгрузки',
			),
			'services'             => array(
				'title'             => 'Дополнительные услуги',
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select-nostd',
				'css'               => 'width: 400px;',
				'desc_tip'          => 'Выберите дополнительные услуги которые будут включены в стоимость доставки.',
				'options'           => wc_edostavka_get_extra_services_list(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите необходимые услуги'
				),
			),
			'information_settings' => array(
				'title' => 'Настройки информации',
				'type'  => 'title'
			),
			'show_delivery_time'   => array(
				'title'    => 'Срок доставки',
				'type'     => 'checkbox',
				'label'    => 'Показывать срок доставки',
				'desc_tip' => 'Отображать предполагаемое время доставки.',
				'default'  => 'no'
			),
			'additional_time'      => array(
				'title'       => 'Добавочные дни доставки',
				'type'        => 'text',
				'desc_tip'    => 'Дополнительные дни к сроку доставки.',
				'default'     => 0,
				'placeholder' => 'Укажите дополнительные дни к доставке',
			),
			'rate_instruction'     => array(
				'title'       => 'Описание метода',
				'type'        => 'textarea',
				'css'         => 'width: 400px;',
				'desc_tip'    => 'Этот текст будут отображаться под названием метода доставки. Оставьте пустым, что бы не отображать.',
				'placeholder' => 'Введите текст который будет описывать какие либо условия доставки по данному методу.'
			),
			'cost_settings'        => array(
				'title' => 'Настройки стоимости',
				'type'  => 'title'
			),
			'fee'                  => array(
				'title'       => 'Наценка на доставку',
				'type'        => 'text',
				'desc_tip'    => 'Введите наценку котороя будет прибавляться к стоимости доставки. Например 250 или 5%. Оставьте пустым что бы не использовать эту опцию.',
				'placeholder' => 'Укажите наценку',
				'default'     => 0,
			),
			'fee_type'             => array(
				'title'    => 'Тип наценки',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'width: 400px;',
				'desc_tip' => 'Выберите от какого значения применять наценку',
				'default'  => 'order',
				'options'  => array(
					'order'    => 'От стоимости заказа',
					'shipping' => 'От стоимости доставки'
				)
			),
			'fee_payments'         => array(
				'title'             => 'Методы оплаты для наценки',
				'placeholder'       => 'Выберите методы оплаты',
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select-nostd',
				'desc_tip'          => 'Выберите способы оплаты, при выборе которых будет применяться наценка. Оставьте поле пустым, чтобы применять наценку всегда.',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите методы оплаты'
				)
			),
			'static_price'         => array(
				'title'       => 'Фиксированная стоимость',
				'type'        => 'price',
				'desc_tip'    => 'Укажите фиксированную стоимость для этого метода. Реальная стоимость СДЭК будет проигнорирована.',
				'placeholder' => 'Укажите фиксированную стоимость',
				'default'     => 0
			),
			'free'                 => array(
				'title'       => 'Бесплатная доставка',
				'type'        => 'price',
				'default'     => 0,
				'desc_tip'    => 'Укажите сумму заказа при достижении которой данный метод доставки будет бесплатным. Оставьте пустым, что бы не использовать эту опцию.',
				'placeholder' => 'Укажите сумму для бесплатной доставки'
			),
			'round_cost'           => array(
				'title'    => 'Округление цены',
				'type'     => 'select',
				'default'  => 'none',
				'desc_tip' => 'Если хотите округлять стоимость доставки в большую или меньшую сторону, то выбирите нужное условие.',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'none'  => 'Не использовать округление',
					'ceil'  => 'Округлять в большую сторону',
					'floor' => 'Округлять в меньшую сторону',
					'round' => 'Округлять в большую или меньшую сторону'
				)
			),
			'round_cost_range'     => array(
				'title'    => 'Шаг округления цены',
				'type'     => 'select',
				'default'  => '10',
				'desc_tip' => 'Выберите до какого значения округлять стоимость',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'10'    => 'До десятков',
					'100'   => 'До сотен',
					'round' => 'До целого числа'
				)
			),
			'available_settings'   => array(
				'title'       => 'Настройки доступности',
				'type'        => 'title',
				'description' => 'Данные параметры влияют на доступность данного метода при установенных условиях.'
			),
			'shipping_class_id'    => array(
				'title'             => 'Класс доставки',
				'type'              => 'select',
				'desc_tip'          => 'При необходимости выберете класс доставки который будет применен к этому методу доставки.',
				'default'           => 'any',
				'class'             => 'wc-enhanced-select',
				'options'           => $this->get_shipping_classes_options(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите класс доставки'
				),
			),
			'min_price'            => array(
				'title'       => 'Минимальная сумма',
				'type'        => 'price',
				'desc_tip'    => 'Установите минимальную сумму заказа после которого будет отображатся этот метод. Оставьте пустым, что бы не использовать эту опцию.',
				'placeholder' => 'Укажите минимальную сумму заказа',
				'default'     => 0
			),
			'max_price'            => array(
				'title'       => 'Максимальная сумма',
				'type'        => 'price',
				'desc_tip'    => 'Установите максимальную сумму заказа до которой будет отображатся этот метод. Оставьте пустым, что бы не использовать эту опцию.',
				'placeholder' => 'Укажите максимальную сумму заказа',
				'default'     => 0
			),
			'location_limit'       => array(
				'title'    => 'Доступность в регионах/городах',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'width: 400px;',
				'default'  => 'none',
				'desc_tip' => 'Данная опция устанавливает правило будет ли данный метод доступен для выбранного региона и городов.',
				'options'  => array(
					'none'    => 'Не использовать',
					'include' => 'Включить для указанных регионов/городов',
					'exclude' => 'Выключить для указанных регионов/городов'
				)
			),
			'states'               => array(
				'title'       => 'Регион',
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'css'         => 'width: 400px;',
				'placeholder' => 'Выберите регион/область/штат',
				'desc_tip'    => 'Выберите регион/область/штат в котором данный метод будет или не будет доступен.',
				'default'     => '',
				'options'     => array()
			),
			'cities'               => array(
				'title'             => 'Города',
				'type'              => 'multiselect',
				'css'               => 'width: 400px;',
				'desc_tip'          => 'Добавьте города для выбранного региона в которых данный метод будет или не будет доступен. Оставьте поле пустым если хотите применять правило целиком к выбранному региону.',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите города',
				),
			),
			'additional_settings'  => array(
				'title' => 'Дополнительные настройки',
				'type'  => 'title'
			),
			'allowed_payments'     => array(
				'title'             => 'Методы оплаты',
				'placeholder'       => 'Выберите методы оплаты',
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select-nostd',
				'desc_tip'          => 'Выберите способы оплаты, которые будут доступны только для этого метода доставки. Оставьте поле пустым, чтобы использовать любой доступный способ оплаты.',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите методы оплаты'
				)
			)
		);

		if ( wc_string_to_bool( get_option( 'woocommerce_enable_coupons' ) ) ) {

			$this->instance_form_fields = Woodev_Helper::array_insert_after( $this->instance_form_fields, 'free', array(
				'coupon_free_shipping' => array(
					'title'    => 'Купон на бесплатную доставку',
					'label'    => sprintf( 'Разрешить использование <a href="%s" target="_blank">купонов на бесплатную доставку</a>', esc_url( admin_url( 'edit.php?post_type=shop_coupon' ) ) ),
					'type'     => 'checkbox',
					'default'  => 'no',
					'desc_tip' => 'Если вы используете купоны с бесплатной доставкой, то вы можете включить эту опцию чтобы сделать данный метод доставки бесплатным при применении купона.'
				)
			) );
		}
	}

	public function generate_multiselect_html( $key, $data ) {

		if ( in_array( $key, array( 'fee_payments', 'allowed_payments' ), true ) && $this->is_accessing_settings() ) {

			$gateways = array();

			if ( WC()->payment_gateways() ) {

				foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {

					$gateway_title = wc_strtolower( trim( $gateway->get_title() ) ) != wc_strtolower( trim( $gateway->get_method_title() ) ) ? sprintf( '%s (%s)', $gateway->get_title(), $gateway->get_method_title() ) : $gateway->get_title();

					$gateways[ $gateway->id ] = $gateway_title;
				}
			}

			$data['options'] = $gateways;
		}

		return parent::generate_multiselect_html( $key, $data );
	}

	public function generate_select_html( $key, $data ) {

		if ( $this->is_accessing_settings() ) {

			$locale = wc_edostavka_get_locale();

			if ( in_array( $key, array( 'sender_city', 'delivery_point' ), true ) ) {

				$base_country   = WC()->countries ? WC()->countries->get_base_country() : 'RU';
				$capital_id     = wc_edostavka_get_country_capital_id( $base_country );
				$sender_city_id = $this->get_option( 'sender_city', $capital_id );

				switch ( $key ) {

					case 'sender_city' :

						$data['default'] = $capital_id;
						$data['options'] = wc_edostavka_get_location_cities_select( array(
							'country_codes' => $base_country,
							'code'          => $sender_city_id,
							'size'          => 1,
							'lang'          => $locale
						) );

						break;

					case 'delivery_point' :

						$data['options'] = wc_edostavka_get_deliverypoints_select( array(
							'city_code'    => $sender_city_id,
							'type'         => 'PVZ',
							'country_code' => $base_country,
							'lang'         => $locale,
							'is_reception' => true
						) );

						break;
				}
			}

			if ( 'states' == $key ) {

				$zone           = WC_Shipping_Zones::get_zone_by( 'instance_id', $this->instance_id );
				$zone_countries = wp_list_pluck( wp_list_filter( $zone->get_zone_locations(), array( 'type' => 'country' ) ), 'code' );

				$data['options'] = array_replace( array( 'any' => 'Любой регион' ), wc_edostavka_get_location_regions_select( array(
					'country_codes' => implode( ',', $zone_countries ),
					'lang'          => $locale
				) ) );

				$data['custom_attributes']['data-countries'] = implode( ',', $zone_countries );
			}
		}

		return parent::generate_select_html( $key, $data );
	}

	public function validate_fee_payments_field( $key, $value ) {

		$instance_payments = ( array ) get_option( WC_CDEK_SHIPPING_FEE_PAYMENT_KEY, array() );
		$post_data         = $this->get_post_data();
		$fee_key           = $this->get_field_key( 'fee' );

		if ( ! empty( $value ) && ! empty( $post_data[ $fee_key ] ) ) {
			$instance_payments[ $this->get_instance_id() ] = $value;
		} elseif ( isset( $instance_payments[ $this->get_instance_id() ] ) ) {
			unset( $instance_payments[ $this->get_instance_id() ] );
		}

		update_option( WC_CDEK_SHIPPING_FEE_PAYMENT_KEY, $instance_payments );

		return $this->validate_multiselect_field( $key, $value );
	}

	public function validate_cities_field( $key, $value ) {

		if ( ! empty( $value ) && is_array( $value ) ) {

			$old = $this->get_instance_option( 'saved_cities', array() );
			$new = array();

			foreach ( $value as $city_code ) {

				if ( in_array( $city_code, array_keys( $old ), true ) ) {
					$new[ $city_code ] = $old[ $city_code ];
				} else {

					$locations = wc_edostavka_get_location_cities( array(
						'code' => $city_code,
						'size' => 1,
						'lang' => wc_edostavka_get_locale()
					) );

					if ( $locations && ! empty( $locations[0] ) ) {
						$new[ $locations[0]->code ] = array(
							'city'         => $locations[0]->city,
							'region_code'  => $locations[0]->region_code,
							'country_code' => $locations[0]->country_code,
							'country'      => $locations[0]->country,
							'region'       => $locations[0]->region,
							'sub_region'   => isset( $locations[0]->sub_region ) ? $locations[0]->sub_region : null
						);
					}
				}
			}

			$this->update_instance_option( 'saved_cities', $new );

		} else {

			$post_data  = $this->get_post_data();
			$states_key = $this->get_field_key( 'states' );
			$limit_key  = $this->get_field_key( 'location_limit' );

			if ( isset( $post_data[ $states_key ], $post_data[ $limit_key ] ) && 'none' !== $post_data[ $limit_key ] && 'any' == $post_data[ $states_key ] ) {
				throw new Exception( '<strong>Настройки не были сохранены!</strong> Так как вы выбрали значение "Любой регион" в опции "Регион", вам необходимо выбрать хотя бы один населённый пункт в поле "Города".' );
			} else {
				$this->update_instance_option( 'saved_cities', array() );
			}
		}

		return $this->validate_multiselect_field( $key, $value );
	}

	public function update_instance_option( $key, $value = '' ) {

		if ( empty( $this->instance_settings ) ) {
			$this->init_instance_settings();
		}

		$this->instance_settings[ $key ] = $value;

		return update_option( $this->get_instance_option_key(), $this->instance_settings, 'yes' );
	}

	/**
	 * @param array $package
	 *
	 * @return bool
	 */
	public function is_available( $package ) {

		if ( ! wc_edostavka_shipping()->get_license_instance()->is_license_valid() ) {
			return false;
		}

		$available = parent::is_available( $package );
		$errors    = array();

		if ( $available && is_cart() && ! wc_string_to_bool( get_option( 'woocommerce_enable_shipping_calc' ) ) && wc_string_to_bool( $this->get_option( 'disable_methods_on_cart', 'no' ) ) ) {
			$available = false;
		}

		if ( $available && ! $this->has_only_selected_shipping_class( $package ) ) {
			$available = false;
			$errors[]  = 'Товары не соответвуют допустимому классу доставки для данного метода';
		}

		if ( $available && empty( trim( $package['destination']['city'] ) ) && ( isset( $package['edostavka_customer_location'], $package['edostavka_customer_location']['city'] ) && empty( $package['edostavka_customer_location']['city'] ) ) ) {
			$available = false;
			$errors[]  = 'Не указан город получатель';
		}

		if ( $available && $this->get_option( 'max_price' ) > 0 && $package['cart_subtotal'] > $this->get_option( 'max_price' ) ) {
			$available = false;
			$errors[]  = sprintf( 'Стоимость товаров в корзине %s привышеют максимально допустимую сумму %s для данного метода', wc_price( $package['cart_subtotal'] ), wc_price( $this->get_option( 'max_price' ) ) );
		}

		if ( $available && $this->get_option( 'min_price' ) > 0 && $package['cart_subtotal'] < $this->get_option( 'min_price' ) ) {
			$available = false;
			$errors[]  = sprintf( 'Стоимость товаров в корзине %s меньше минимально допустимой суммы %s для данного метода', wc_price( $package['cart_subtotal'] ), wc_price( $this->get_option( 'min_price' ) ) );
		}

		if ( $available ) {

			$tariff_data = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $this->get_option( 'tariff' ) );

			if ( isset( $tariff_data['max_weight'] ) && $tariff_data['max_weight'] > 0 ) {
				$packages       = $this->get_packages( $package );
				$package_weight = wc_get_weight( array_sum( wp_list_pluck( $packages, 'weight' ) ), 'kg', 'g' );

				if ( $package_weight > $tariff_data['max_weight'] ) {

					$available = false;

					$errors[] = sprintf( 'Вес товаров %d кг. привышает максимально допустимый вес %d кг. для данного тарифа.', $package_weight, $tariff_data['max_weight'] );
				}
			}
		}

		if ( $available ) {

			$location_limit = $this->get_option( 'location_limit' );
			$states_limit   = $this->get_option( 'states' );
			$cities_limit   = $this->get_option( 'cities', array() );

			try {

				$customer_locations = wc_edostavka_shipping()->get_customer_handler();
				$country_code       = ! empty( $package['destination']['country'] ) ? $package['destination']['country'] : WC()->customer->get_default_country();

				if ( ! empty( $customer_locations->get_city_code() ) && $customer_locations->get_country_code() == $country_code ) {
					$this->locations_to['code']        = $customer_locations->get_city_code();
					$this->locations_to['region_code'] = $customer_locations->get_region_code();

				} elseif ( ! empty( trim( $package['destination']['city'] ) ) ) {

					$cities = wc_edostavka_get_location_cities( array(
						'country_codes' => $country_code,
						'city'          => trim( $package['destination']['city'] ),
						'size'          => 1
					) );

					if ( ! empty( $cities ) && ! empty( $cities[0] ) ) {

						$city = array_shift( $cities );

						$customer_locations->set_location( array(
							'country_code' => $city->country_code,
							'region_code'  => $city->region_code,
							'region'       => $city->region,
							'city_code'    => $city->code,
							'city'         => $city->city,
							'longitude'    => $city->longitude,
							'latitude'     => $city->latitude
						) );

						//$customer_locations->save();

						$this->locations_to['code']        = $customer_locations->get_city_code();
						$this->locations_to['region_code'] = $customer_locations->get_region_code();
					}
				}

			} catch ( Exception $e ) {
				$available = false;
				$errors[]  = $e->getMessage();
			}

			if ( ! isset( $this->locations_to['code'] ) || empty( $this->locations_to['code'] ) ) {
				$available = false;
				$errors[]  = 'Не удалось получить код города получателя';
			}

			if ( 'none' !== $location_limit && ! empty( $this->locations_to ) && ! empty( $states_limit ) ) {

				$states_is_equal = in_array( $states_limit, array( 'any', $this->locations_to['region_code'] ) );
				$cities_is_equal = in_array( $this->locations_to['code'], array_values( $cities_limit ) );

				switch ( $location_limit ) {
					case 'include' :
						if ( empty( $cities_limit ) && ! $states_is_equal ) {
							$available = false;
							$errors[]  = 'Регион покупателя не доступен для данного метода';
						} elseif ( ! empty( $cities_limit ) && ! $cities_is_equal ) {
							$available = false;
							$errors[]  = 'Город покупателя не доступен для данного метода';
						}
						break;
					case 'exclude' :
						if ( empty( $cities_limit ) && $states_is_equal ) {
							$available = false;
							$errors[]  = 'Регион покупателя не доступен для данного метода';
						} elseif ( ! empty( $cities_limit ) && $cities_is_equal ) {
							$available = false;
							$errors[]  = 'Город покупателя не доступен для данного метода';
						}
						break;
				}
			}
		}

		if ( ! $available && ! empty( $errors ) ) {
			wc_edostavka_shipping()->log( sprintf( 'Процесс рассчёта стоимости доставки для метода "%s" был приостановлен из-за установленных ограничений: %s.', $this->title, implode( ', ', $errors ) ) );
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $available, $package, $this );
	}

	private function has_only_selected_shipping_class( $package ) {
		$only_selected     = true;
		$shipping_class_id = $this->get_option( 'shipping_class_id' );

		if ( 'any' == $shipping_class_id ) {
			return true;
		}

		foreach ( $package['contents'] as $item_id => $values ) {
			$product = $values['data'];
			$qty     = $values['quantity'];

			if ( $qty > 0 && $product->needs_shipping() ) {

				if ( $product->get_shipping_class_id() != $shipping_class_id ) {
					$only_selected = false;
					break;
				}
			}
		}

		return $only_selected;
	}

	public function calculate_shipping( $package = array() ) {

		if ( ! $this->should_send_cart_api_request() ) {
			return;
		}

		$rate = $this->dispatch( $package );

		if ( $rate && ! $this->has_rate_error( $rate ) ) {

			$total_cost = apply_filters( 'wc_edostavka_delivery_rates_item_total_cost', array_sum( array(
				$rate['total_sum'],
				$rate['boxes_cost']
			) ), $rate, $package );

			$rate_cost = $this->get_rate_cost( $total_cost, $package );

			if ( $rate_cost > 0 && $this->get_option( 'round_cost', 'none' ) != 'none' ) {

				$round_function = $this->get_option( 'round_cost' );
				$round_range    = $this->get_option( 'round_cost_range', '10' );

				if ( 'round' == $round_function && 'round' == $round_range ) {
					$rate_cost = round( $rate_cost );
				} elseif ( function_exists( $round_function ) && intval( $round_range ) > 0 ) {
					$rate_cost = $round_function( $rate_cost / intval( $round_range ) ) * intval( $round_range );
				}
			}

			$rate_atts = apply_filters( 'wc_edostavka_delivery_rates_item', array(
				'id'        => $this->get_rate_id( $this->get_option( 'tariff' ) ),
				'label'     => $this->get_option( 'title' ),
				'cost'      => $rate_cost,
				'package'   => $package,
				'meta_data' => array(
					'edostavka_rate' => $rate
				)
			), $rate, $package, $this );

			$this->add_rate( $rate_atts );
		}

		do_action( 'wc_edostavka_delivery_rate_calculate_shipping', $rate, $package, $this );
	}

	private function dispatch( $package = array() ) {

		$currency      = $this->get_option( 'currency', 'default' );
		$currency_code = 'default' == $currency ? get_woocommerce_currency() : $currency;
		$packages      = $this->get_packages( $package );

		$calc_params = array(
			'currency'      => wc_edostavka_get_currency_code( $currency_code ),
			'tariff_code'   => $this->get_option( 'tariff' ),
			'from_location' => array( 'code' => $this->get_option( 'sender_city' ) ),
			'to_location'   => $this->locations_to,
			'packages'      => array_map( function ( $param ) {
				return array_intersect_key( $param, array_flip( array( 'weight', 'length', 'width', 'height' ) ) );
			}, $packages )
		);

		if ( ! empty( $this->get_option( 'services' ) ) ) {
			foreach ( $this->get_option( 'services' ) as $service ) {

				if ( 'PART_DELIV' == wc_strtoupper( $service ) && $this->get_items_count( $package ) > 1 ) {
					continue;
				}

				if ( $service == 'INSURANCE' ) {
					$calc_params['services'][] = array(
						'code'      => $service,
						'parameter' => $package['contents_cost']
					);
				} elseif ( in_array( wc_strtoupper( $service ), array(
					'PHOTO_DOCUMENT',
					'CARTON_FILLER',
					'COURIER_PACKAGE_A2',
					'SECURE_PACKAGE_A2',
					'SECURE_PACKAGE_A3',
					'SECURE_PACKAGE_A4',
					'SECURE_PACKAGE_A5'
				), true ) ) {
					$calc_params['services'][] = array(
						'code'      => $service,
						'parameter' => 1
					);
				} elseif ( in_array( wc_strtoupper( $service ), array( 'WASTE_PAPER', 'BUBBLE_WRAP' ), true ) ) {
					$calc_params['services'][] = array(
						'code'      => $service,
						'parameter' => 1
						//TODO: Этот параметр нужно будет динамически расчитать. Пока что фикс значение.
					);
				} elseif ( 'GET_UP_FLOOR_BY_HAND' == wc_strtoupper( $service ) ) {

					$calc_params['services'][] = array(
						'code'      => $service,
						'parameter' => 4 //количество этажей
						//TODO: Этот параметр нужно будет динамически расчитать. Пока что фикс значение.
					);

				} else {
					$calc_params['services'][] = array( 'code' => $service );
					//TODO: для других услуг тоже описать условия
				}
			}
		}

		$added_boxes  = array();
		$boxes_cost   = 0;
		$tariff_data  = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $calc_params['tariff_code'] );
		$is_from_door = in_array( $tariff_data['type'], array( 'door_door', 'door_stock', 'door_postamat' ), true );

		foreach ( $packages as $package_box ) {

			if ( isset( $package_box['id'] ) && ! empty( $package_box['id'] ) ) {

				if ( ! $is_from_door && in_array( $package_box['id'], array_keys( wc_edostavka_get_carton_boxes() ), true ) && true === $package_box['cost'] ) {

					if ( in_array( $package_box['id'], $added_boxes, true ) ) {
						continue;
					}

					$same_boxes = wp_list_filter( $packages, array( 'id' => $package_box['id'] ) );

					$calc_params['services'][] = array(
						'code'      => $package_box['id'],
						'parameter' => count( $same_boxes )
					);

					$added_boxes[] = $package_box['id'];

				} elseif ( isset( $package_box['cost'] ) && $package_box['cost'] > 0 ) {
					$boxes_cost += intval( $package_box['cost'] );
				}
			}
		}

		$calc_params = apply_filters( 'wc_cdek_shipping_rates_calc_params', $calc_params, $package );

		$cache_key = sprintf( 'wc_cdek_shipping_rates_options_%s', md5( serialize( $calc_params ) ) );
		$rates     = wp_cache_get( $cache_key );

		if ( false === $rates || wc_string_to_bool( get_option( 'woocommerce_shipping_debug_mode', 'no' ) ) ) {

			try {

				$rates = wc_edostavka_shipping()->get_api()->calculate_tariff( $calc_params )->get_tariff();

				if ( ! $this->has_rate_error( $rates ) ) {
					$rates['boxes_cost'] = $boxes_cost;
					wp_cache_set( $cache_key, $rates, '', 3600 );
				}

			} catch ( Woodev_API_Exception $e ) {

				if ( wc_string_to_bool( $this->get_option( 'enable_debug', 'no' ) ) && current_user_can( 'manage_woocommerce' ) ) {

					$error_notice = sprintf( __( 'An error occurred in processing calculate shipping cost by CDEK. %s', 'woocommerce-edostavka' ), $e->getMessage() );

					if ( ! wc_has_notice( $error_notice, 'notice' ) ) {
						wc_add_notice( $error_notice, 'notice' );
					}

					if ( $e->getCode() > 400 ) {
						$error_reason = sprintf( __( 'The error "%s" might be called because server of CDEK is failed. You can disable CDEK shipping methods until it will be fixed.', 'woocommerce-edostavka' ), $e->getMessage() );
						if ( ! wc_has_notice( $error_reason, 'notice' ) ) {
							wc_add_notice( $error_reason, 'notice' );
						}
					}
				}

				wc_edostavka_shipping()->log( $e->getMessage() );
			}
		}

		return $rates;
	}

	public function get_rate_cost( $cost, $package = array() ) {

		if ( $this->get_option( 'static_price' ) > 0 ) {
			return $this->get_option( 'static_price' );
		}

		if ( wc_string_to_bool( get_option( 'woocommerce_enable_coupons' ) ) && WC()->cart && wc_string_to_bool( $this->get_option( 'coupon_free_shipping', 'no' ) ) ) {

			/** @var WC_Coupon[] $coupons */
			$coupons = WC()->cart->get_coupons();

			if ( $coupons ) {
				foreach ( $coupons as $code => $coupon ) {
					if ( $coupon->is_valid() && $coupon->get_free_shipping() ) {
						return wc_format_localized_price( 0 );
					}
				}
			}
		}

		$fee          = 0;
		$free         = $this->get_option( 'free', 0 );
		$method_fee   = $this->get_option( 'fee' );
		$fee_type     = $this->get_option( 'fee_type', 'order' );
		$fee_payments = $this->get_option( 'fee_payments', array() );

		if ( ! empty( $method_fee ) && ( $free == 0 || ( floatval( $free ) > floatval( $package['contents_cost'] ) ) ) ) {

			if ( ! $fee_payments || in_array( $package['chosen_payment_method'], $fee_payments, true ) ) {
				$fee = $this->get_fee( $method_fee, $fee_type == 'order' ? $package['contents_cost'] : $cost );
			}

		} elseif ( $free > 0 && floatval( $package['contents_cost'] ) > floatval( $free ) ) {
			return wc_format_localized_price( 0 );
		}

		return array_sum( array( $cost, $fee ) );
	}

	public function should_send_cart_api_request() {
		return ! (
			( is_admin() && did_action( 'woocommerce_cart_loaded_from_session' ) ) ||
			( defined( 'REST_REQUEST' ) || defined( 'REST_API_REQUEST' ) || defined( 'XMLRPC_REQUEST' ) )
		);
	}

	/**
	 * @param array $rate_data
	 *
	 * @return bool
	 */
	private function has_rate_error( $rate_data ) {

		if ( empty( $rate_data ) || ! isset( $rate_data['total_sum'] ) ) {

			wc_edostavka_shipping()->log( sprintf(
				__( 'Error. Unable to get shipping rate(s) for %s instance id %d.', 'woocommerce-edostavka' ),
				$this->id,
				$this->instance_id
			) );

			return true;
		}

		return false;
	}

	public function get_items_count( array $package ) {
		$count = 0;
		foreach ( $package['contents'] as $item ) {
			if ( ! $item['data']->needs_shipping() ) {
				continue;
			}
			$count += $item['quantity'];
		}

		return $count;
	}

	public function get_packages( $package ) {

		$packages = array();
		$packer   = wc_edostavka_get_package_box_handler();

		foreach ( $package['contents'] as $item_id => $values ) {

			/** @var WC_Product $product */
			$product = $values['data'];
			/** @var integer $qty */
			$qty = $values['quantity'];

			if ( ! $product->needs_shipping() ) {
				wc_edostavka_shipping()->log( sprintf( 'Товар %s не был добавлен в расчёт стоимости доставки так как он является виртуальным.', $item_id ) );
				continue;
			}

			$default_weight = $this->get_option( 'default_weight' );
			$default_height = $this->get_option( 'default_height' );
			$default_width  = $this->get_option( 'default_width' );
			$default_length = $this->get_option( 'default_length' );

			$product_weight = $product->has_weight() ? wc_get_weight( $product->get_weight(), 'g' ) : $default_weight;
			$product_height = $product->get_height() > 0 ? wc_get_dimension( $product->get_height(), 'cm' ) : $default_height;
			$product_width  = $product->get_width() > 0 ? wc_get_dimension( $product->get_width(), 'cm' ) : $default_width;
			$product_length = $product->get_length() > 0 ? wc_get_dimension( $product->get_length(), 'cm' ) : $default_length;

			for ( $i = 0; $i < $qty; $i ++ ) {

				$new_item = new Woodev_Packer_Item_Implementation(
					ceil( $product_length ),
					ceil( $product_width ),
					ceil( $product_height ),
					intval( $product_weight ),
					$product->get_price()
				);

				$new_item->set_product( $product );

				$packer->add_item( $new_item );
			}
		}

		$packaged = $packer->get_packages();

		foreach ( ( array ) $packaged['packages'] as $packed ) {

			$new_package = array(
				'id'     => $packed['box']['id'],
				'weight' => $packed['weight'],
				'length' => $packed['box']['length'],
				'width'  => $packed['box']['width'],
				'height' => $packed['box']['height'],
				'volume' => $packed['box']['volume'],
				'value'  => $packed['value']
			);

			if ( isset( $packed['box']['internal_data'], $packed['box']['internal_data']['cost'] ) ) {

				$box_cost = $packed['box']['internal_data']['cost'];

				if ( is_bool( $box_cost ) ) {
					$new_package['cost'] = ( boolean ) $box_cost;
				} elseif ( is_string( $box_cost ) ) {

					if ( strstr( $box_cost, '%' ) ) {
						$new_package['cost'] = ( $packed['value'] / 100 ) * str_replace( '%', '', $box_cost );
					} else {
						$new_package['cost'] = intval( $box_cost );
					}
				}
			}

			$packages[] = $new_package;
		}

		return $packages;
	}

	private function get_tariffs_list() {

		$list        = array();
		$all_tariffs = WC_Edostavka_Tariffs_Data::get_all_tariffs();

		foreach ( $all_tariffs as $group => $tariff ) {
			$list[ WC_Edostavka_Tariffs_Data::get_group_name( $group ) ] = wp_list_pluck( $tariff, 'name', 'code' );
		}

		return $list;
	}

	private function get_shipping_classes_options() {
		$shipping_classes = WC()->shipping()->get_shipping_classes();
		$options          = array(
			'any'  => 'Любой класс доставки',
			'none' => 'Без класса доставки'
		);

		if ( ! empty( $shipping_classes ) ) {
			$options += wp_list_pluck( $shipping_classes, 'name', 'term_id' );
		}

		return $options;
	}

	public function zone_shipping_methods( $methods ) {
		if ( isset( $_REQUEST['zone_id'], $methods[ $this->instance_id ] ) && ! wc_edostavka_shipping()->get_license_instance()->is_active() ) {
			$error_message                                     = sprintf(
				__( 'This shipping method will not working, because the licence key is expired or it entered incorrect. Please, go to <a href="%s">page of plugin licences</a> to enter actual licence key.', 'woocommerce-edostavka' ),
				wc_edostavka_shipping()->get_license_instance()->get_license_settings_url()
			);
			$methods[ $this->instance_id ]->method_description .= sprintf( '<div class="woocommerce-message error">%s</div>', $error_message );
		}

		return $methods;
	}

	/**
	 * Checks to see whether or not the admin settings are being accessed by the current request.
	 *
	 * @return bool
	 */
	private function is_accessing_settings() {
		if ( is_admin() ) {

			if ( ! isset( $_REQUEST['page'] ) || 'wc-settings' !== $_REQUEST['page'] ) {
				return false;
			}
			if ( ! isset( $_REQUEST['tab'] ) || 'shipping' !== $_REQUEST['tab'] ) {
				return false;
			}
			if ( $this->supports( 'instance-settings' ) && ( ! isset( $_REQUEST['instance_id'] ) || absint( $_REQUEST['instance_id'] ) !== $this->get_instance_id() ) ) {
				return false;
			}

			return true;
		}

		return false;
	}
}
