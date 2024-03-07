<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return array of regions by params as looks like region_code => region_name
 *
 * @param $params array
 *
 * @return array
 */
function wc_edostavka_get_location_regions_select( $params = array() ) {
	$options = array();

	foreach ( ( array ) wc_edostavka_get_location_regions( $params ) as $region ) {
		if ( isset( $region->region_code, $region->region ) ) {
			$options[ $region->region_code ] = $region->region;
		}
	}

	return $options;
}

/**
 * Return array of cities by params as looks like region_code => region_name
 *
 * @param $params array
 *
 * @return array
 */
function wc_edostavka_get_location_cities_select( $params = array(), $show_region = true ) {
	$options = array();

	foreach ( ( array ) wc_edostavka_get_location_cities( $params ) as $city ) {
		if ( in_array( $city->code, wc_edostavka_get_disallow_cities_ids(), true ) ) {
			continue;
		}
		$name                   = ( $show_region && $city->region ) ? sprintf( '%s (%s)', $city->city, $city->region ) : $city->city;
		$options[ $city->code ] = $name;
	}

	return $options;
}

/**
 * @param array $params
 *
 * @return array
 */
function wc_edostavka_get_deliverypoints_select( $params = array() ) {
	$options = array();

	foreach ( ( array ) wc_edostavka_get_deliverypoints( $params ) as $point ) {
		if ( ! empty( $point->code ) && ! empty( $point->name ) ) {
			$options[ $point->code ] = sprintf( '[%s] %s (%s)', $point->code, $point->name, ( $point->location && $point->location->address_full ? $point->location->address_full : __( 'N/A', 'woocommerce' ) ) );
		}
	}

	return $options;
}

/**
 * Returns capital city ID by country code
 *
 * @param string $country_code Country code like as RU, BY, KZ etc.
 *
 * @return int|null
 */
function wc_edostavka_get_country_capital_id( $country_code = '' ) {
	if ( ! $country_code ) {
		$country_code = WC()->countries->get_base_country();
	}

	$capitals = array(
		'RU' => 44,      //Россия - Москва
		'BY' => 9220,    //Белорусь - Минск
		'KZ' => 4961,    //Казахстан - Нур-султан
		'UZ' => 11562,   //Узбекистан - Ташкент
		'UA' => 7870,    //Украина - Киев
		'AM' => 7114,    //Армения - Ереван
		'KG' => 5444,    //Киргистан - Бишкек
		'AZ' => 5090,    //Азербайджан - Баку
		'GE' => 11564    //Грузия - Тбилиси
	);

	return isset( $capitals[ $country_code ] ) ? $capitals[ $country_code ] : null;
}

/**
 * Returns unused city ids
 *
 * @return array
 */
function wc_edostavka_get_disallow_cities_ids() {
	return apply_filters( 'woocommerce_edostavka_disallow_city_ids', array(
		79395,
		76380,
		55949,
		78194,
		80655
	) );
}

/**
 * @return array
 */
function wc_edostavka_get_preloaded_data_locations() {
	return apply_filters( 'wc_edostavka_preloaded_data_locations', array(
		'RU' => array(
			44, // Москва
			137, // Питер
			424, // Казань
			94, //Владимир
			506, //Воронеж
			426, //Волгоград
			250, //Екатеринбург
			152, //Калининград
			278, //Красноярск
			270, //Новосибирск
			414, //Нижний Новгород
			268, //Омск
			248, //Пермь
			438, //Ростов-на-дону
			437, //Сочи
			430, //Самара
			256, //Уфа
			259 //Челябинск
		),
		'BY' => array(
			9220, //Минск
			6539, //Гомель
			9263, //Могилев
			6215, //Витебск
			6668, //Гродно
			5624 //Брест
		),
		'KZ' => array(
			4961, //Нур-Султан (Астана)
			4756, //Алматы
			12787, //Шымкент
			7669 //Караганда
		),
		'UZ' => array(
			11562, //Ташкент
			10822, //Самарканд
			75382, //Бухара
			9428, //Наманган
			75391, //Андижан
			75414, //Нукус
			75376, //Карши
			63290, //Коканд
			11969, //Фергана
			12620, //Чирчик
			75375, //Навои
			75413, //Термез
			52016 //Алмалык
		),
		'UA' => array(
			7870, //Киев
			12262, //Харьков
			9816, //Одеса
			6953, //Днепр
			883777 //Донецк
		),
		'AM' => array(
			7114, //Ереван
			31223, //Армавир
			840577, //Арарат
		),
		'KG' => array(
			5444 //Бишкек
		),
		'AZ' => array(
			5090 //Баку
		),
		'GE' => array(
			11564 //Тбилиси
		)
	) );
}

/**
 * @param $country_code - Code of needed country
 *
 * @return array
 */
function wc_edostavka_get_preloaded_locations( $country_code = 'RU' ) {
	$locations_data = wc_edostavka_get_preloaded_data_locations();
	$locations      = array();

	if ( isset( $locations_data[ $country_code ] ) && is_array( $locations_data[ $country_code ] ) ) {
		foreach ( $locations_data[ $country_code ] as $code ) {
			$cities = wc_edostavka_get_location_cities( array(
				'country_codes' => $country_code,
				'code'          => $code,
				'size'          => 1,
				'lang'          => wc_edostavka_get_locale()
			) );

			if ( $cities && ! empty( $cities[0] ) && $cities[0]->country_code == $country_code ) {
				$locations[] = $cities[0];
			}
		}
	}

	return $locations;
}

/**
 * @param string $locale
 *  $locate should be equals shop locale, like this ru_RU, en_US etc.
 *
 * @return string
 */
function wc_edostavka_get_locale( $locale = '' ) {

	if ( ! $locale ) {
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	}

	return apply_filters( 'wc_edostavka_locale', substr( $locale, 0, 2 ) == 'ru' ? 'rus' : 'eng' );
}

/**
 * @param string $code Currency code
 *
 * @return int|string
 */
function wc_edostavka_get_currency_code( $code = 'RUB' ) {
	$currencies = apply_filters( 'wc_edostavka_currencies', array(
		'RUB' => 1,
		'KZT' => 2,
		'USD' => 3,
		'EUR' => 4,
		'GBP' => 5,
		'CNY' => 6,
		'BYN' => 7,
		'UAH' => 8,
		'KGS' => 9,
		'AMD' => 10,
		'TRY' => 11,
		'KRW' => 13,
		'AED' => 14,
		'UZS' => 15,
		'MNT' => 16,
		'PLN' => 17,
		'AZN' => 18,
		'GEL' => 19
	) );

	return isset( $currencies[ $code ] ) ? $currencies[ $code ] : $code;
}


/**
 * @param boolean $show_symbol Show or not currency symbol within currency name.
 *
 * @return array Array of currencies allowed in CDEK
 */
function wc_edostavka_get_allowed_currencies( $show_symbol = false ) {
	$all_currency = get_woocommerce_currencies();
	$allowed      = array();

	foreach ( $all_currency as $code => $name ) {
		if ( ! wc_edostavka_get_currency_code( $code ) || $code == wc_edostavka_get_currency_code( $code ) ) {
			continue;
		}
		$allowed[ $code ] = $show_symbol ? sprintf( '%s (%s)', $name, get_woocommerce_currency_symbol( $code ) ) : $name;
	}

	return $allowed;
}

/**
 * Wrapper is_admin() function within apply wc_edostavka_is_admin hook
 * @return bool
 * @see is_admin()
 */
function wc_edostavka_is_admin_scope() {
	return apply_filters( 'wc_edostavka_is_admin', is_admin() );
}

/**
 * @return bool
 */
function edostavka_only_virtual_products_in_cart() {

	$only_virtual = false;

	if ( WC()->cart ) {

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['data']->is_virtual() ) {
				$only_virtual = true;
			}
		}

	}

	return $only_virtual;
}

/**
 * @return array
 */
function wc_edostavka_get_allowed_zone_locations() {
	global $wpdb;

	return $wpdb->get_col( $wpdb->prepare( "
		SELECT DISTINCT zone_locations.location_code FROM {$wpdb->prefix}woocommerce_shipping_zone_locations AS zone_locations
		LEFT JOIN {$wpdb->prefix}woocommerce_shipping_zone_methods AS zone_methods ON zone_methods.zone_id = zone_locations.zone_id
		WHERE zone_locations.location_type = 'country'
		AND zone_methods.method_id = %s
		AND zone_methods.is_enabled = 1
	", wc_edostavka_shipping()->get_method_id() ) );
}

/**
 * @return array
 * @throws Exception When validation fails.
 * @since 2.2.0
 */
function wc_edostavka_get_customer_location() {

	try {

		$customer_handler   = wc_edostavka_shipping()->get_customer_handler();
		$customer_locations = $customer_handler->get_location();

		if ( empty( $customer_handler->get_city_code() ) ) {

			$customer_default_city = wc_edostavka_shipping()->get_integration_handler()->get_option( 'customer_default_city' );
			$customer_city         = WC()->customer ? WC()->customer->get_billing_city() : __return_empty_string();
			$city_location_params  = array(
				'country_codes' => WC()->customer ? WC()->customer->get_billing_country() : WC()->countries->get_base_country(),
				'size'          => 1,
				'lang'          => wc_edostavka_get_locale()
			);
			$need_search_location  = false;

			if ( ! empty( $customer_city ) ) {
				$city_location_params['city'] = trim( $customer_city );
				$need_search_location         = true;
			} elseif ( ! empty( $customer_default_city ) ) {
				$city_location_params['code'] = $customer_default_city;
				$need_search_location         = true;
			}

			if ( $need_search_location ) {

				$city_location = wc_edostavka_get_location_cities( $city_location_params );

				if ( $city_location && $city_location[0] ) {

					$customer_locations = array(
						'country_code' => $city_location[0]->country_code,
						'region_code'  => $city_location[0]->region_code,
						'region'       => $city_location[0]->region,
						'city_code'    => $city_location[0]->code,
						'city'         => $city_location[0]->city,
						'longitude'    => $city_location[0]->longitude,
						'latitude'     => $city_location[0]->latitude
					);

					$customer_handler->set_location( $customer_locations );
					//$customer_handler->save();

				} else {
					throw new Exception( sprintf( 'Не удалось получить населённый пункт по ID: %d', $customer_default_city ) );
				}
			}
		}

		return $customer_locations;

	} catch ( Exception $exception ) {
		wc_edostavka_shipping()->log( $exception->getMessage() );

		return __return_empty_array();
	}
}

/**
 * @param array $location Array containing the necessary params from WC_Edostavka_Customer::data
 *
 * @return void
 * @throws Exception When validation fails.
 * @since 2.2.0
 * @see WC_Edostavka_Customer::data Array structure
 */
function wc_edostavka_set_customer_location( $location = array() ) {

	try {

		wc_edostavka_shipping()->get_customer_handler()->set_location( $location );
		//wc_edostavka_shipping()->get_customer_handler()->save();

	} catch ( Exception $exception ) {
		wc_edostavka_shipping()->log( $exception->getMessage() );
	}


	if ( apply_filters( 'wc_edostavka_customer_set_default_destination', true, $location, WC()->customer ) ) {

		if ( ! empty( $location['country_code'] ) ) {
			WC()->customer->set_shipping_country( $location['country_code'] );
			WC()->customer->set_billing_country( $location['country_code'] );
		}

		if ( ! empty( $location['region'] ) ) {
			WC()->customer->set_shipping_state( $location['region'] );
			WC()->customer->set_billing_state( $location['region'] );
		}

		if ( ! empty( $location['city'] ) ) {
			WC()->customer->set_shipping_city( $location['city'] );
			WC()->customer->set_billing_city( $location['city'] );
		}

		if ( WC()->customer->get_changes() ) {
			WC()->customer->save();
		}
	}

}

/**
 * @return array
 */
function wc_edostavka_get_customer_location_fields() {
	return apply_filters( 'wc_edostavka_customer_location_fields', array(
		'country_code',
		'region_code',
		'region',
		'city_code',
		'city',
		'longitude',
		'latitude'
	) );
}

/**
 * @param boolean $need_instance If set true and chosen shipping method is CDEK, returns an instance of WD_Edostavka_Shipping class else returns just boolean
 *
 * @return bool|WD_Edostavka_Shipping
 */
function wc_edostavka_chosen_rate_is_edostavka_shipping( $need_instance = false ) {

	if ( WC()->session ) {
		$shipping_packages               = WC()->shipping()->get_packages();
		$chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );
		if ( ! empty( $chosen_shipping_methods_session ) && is_array( $chosen_shipping_methods_session ) ) {
			foreach ( $chosen_shipping_methods_session as $package_key => $chosen_package_rate_id ) {
				if ( ! empty( $shipping_packages[ $package_key ]['rates'][ $chosen_package_rate_id ] ) ) {
					$chosen_rate = $shipping_packages[ $package_key ]['rates'][ $chosen_package_rate_id ];
					if ( $chosen_rate->get_method_id() == wc_edostavka_shipping()->get_method_id() ) {

						if ( $need_instance ) {
							return $chosen_rate->get_instance_id();
						}

						return true;
					}
				}
			}
		}
	}

	return false;
}

/**
 * @return bool|WC_Shipping_Method
 */
function wc_edostavka_get_chosen_method_instance() {

	$is_frontend = is_cart() || is_account_page() || is_checkout() || is_customize_preview();

	if ( $is_frontend && WC()->session ) {

		$chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );

		if ( ! empty( $chosen_shipping_methods_session ) && is_array( $chosen_shipping_methods_session ) ) {

			$method_instance = false;

			foreach ( $chosen_shipping_methods_session as $package_key => $chosen_package_rate_id ) {

				if ( Woodev_Helper::str_exists( $chosen_package_rate_id, ':' ) ) {

					list( $method_id, $instance_id ) = explode( ':', $chosen_package_rate_id );

					if ( $method_id == wc_edostavka_shipping()->get_method_id() ) {
						$method_instance = WC_Shipping_Zones::get_shipping_method( $instance_id );
						break;
					}
				}
			}

			if ( $method_instance ) {
				return $method_instance;
			}
		}
	}

	return false;
}

/**
 * @return array Array like as key => value of extra services
 */
function wc_edostavka_get_extra_services_list() {
	return apply_filters( 'wc_edostavka_extra_services_list', array(
		'INSURANCE'                           => 'Страхование груза',
		//'TAKE_SENDER'               => 'Забор в городе отправителе',
		//'DELIV_RECEIVER'            => 'Доставка в городе получателе',
		'TRYING_ON'                           => 'Примерка на дому',
		'PART_DELIV'                          => 'Частичная доставка',
		'REVERSE'                             => 'Реверс',
		'DANGER_CARGO'                        => 'Опасный груз',
		'SMS'                                 => 'Смс уведомление',
		'CALL'                                => 'Прозвон',
		'THERMAL_MODE'                        => 'Тепловой режим',
		'NOTIFY_ORDER_CREATED'                => 'Уведомление о создании заказа в СДЭК',
		'NOTIFY_ORDER_DELIVERY'               => 'Уведомление о приеме заказа на доставку',
		'COURIER_PACKAGE_A2'                  => 'Пакет курьерский А2',
		'SECURE_PACKAGE_A2'                   => 'Сейф пакет А2',
		'SECURE_PACKAGE_A3'                   => 'Сейф пакет А3',
		'SECURE_PACKAGE_A4'                   => 'Сейф пакет А4',
		'SECURE_PACKAGE_A5'                   => 'Сейф пакет А5',
		'BUBBLE_WRAP'                         => 'Воздушно-пузырчатая пленка',
		'WASTE_PAPER'                         => 'Макулатурная бумага',
		'CARTON_FILLER'                       => 'Прессованный картон "филлер" (55х14х2,3 см)',
		'BAN_ATTACHMENT_INSPECTION'           => 'Запрет осмотра вложения',
		'PHOTO_DOCUMENT'                      => 'Фото документов',
		'GET_UP_FLOOR_BY_HAND'                => 'Подъём на этаж (по лестнице)',
		'GET_UP_FLOOR_BY_ELEVATOR'            => 'Подъём на этаж (на лифте)',
		'ADULT_GOODS'                         => 'Проверка возраста получателя (товары 18+)',
		'LOADING_OPERATIONS_AT_THE_SENDER'    => 'Погрузо-разгрузочные работы у отправителя',
		'LOAD_THE_OPERATION_AT_THE_RECIPIENT' => 'Погрузо-разгрузочные работы у получателя'
	) );
}

function wc_edostavka_get_carton_boxes() {
	return apply_filters( 'wc_edostavka_carton_boxes', array(
		'CARTON_BOX_XS'    => array(
			'name'   => __( 'Box XS', 'woocommerce-edostavka' ),
			'weight' => 0.5,
			'length' => 17,
			'width'  => 12,
			'height' => 9
		),
		'CARTON_BOX_S'     => array(
			'name'   => __( 'Box S', 'woocommerce-edostavka' ),
			'weight' => 2,
			'length' => 21,
			'width'  => 20,
			'height' => 11
		),
		'CARTON_BOX_M'     => array(
			'name'   => __( 'Box M', 'woocommerce-edostavka' ),
			'weight' => 5,
			'length' => 33,
			'width'  => 25,
			'height' => 15
		),
		'CARTON_BOX_L'     => array(
			'name'   => __( 'Box L', 'woocommerce-edostavka' ),
			'weight' => 12,
			'length' => 34,
			'width'  => 33,
			'height' => 26
		),
		'CARTON_BOX_500GR' => array(
			'name'   => __( 'Box 500G', 'woocommerce-edostavka' ),
			'weight' => 0.5,
			'length' => 17,
			'width'  => 12,
			'height' => 10
		),
		'CARTON_BOX_1KG'   => array(
			'name'   => __( 'Box 1 KG', 'woocommerce-edostavka' ),
			'weight' => 1,
			'length' => 24,
			'width'  => 17,
			'height' => 10
		),
		'CARTON_BOX_2KG'   => array(
			'name'   => __( 'Box 2 KG', 'woocommerce-edostavka' ),
			'weight' => 2,
			'length' => 34,
			'width'  => 24,
			'height' => 10
		),
		'CARTON_BOX_3KG'   => array(
			'name'   => __( 'Box 3 KG', 'woocommerce-edostavka' ),
			'weight' => 3,
			'length' => 24,
			'width'  => 24,
			'height' => 21
		),
		'CARTON_BOX_5KG'   => array(
			'name'   => __( 'Box 5 KG', 'woocommerce-edostavka' ),
			'weight' => 5,
			'length' => 40,
			'width'  => 24,
			'height' => 21
		),
		'CARTON_BOX_10KG'  => array(
			'name'   => __( 'Box 10 KG', 'woocommerce-edostavka' ),
			'weight' => 10,
			'length' => 40,
			'width'  => 35,
			'height' => 28
		),
		'CARTON_BOX_15KG'  => array(
			'name'   => __( 'Box 15 KG', 'woocommerce-edostavka' ),
			'weight' => 15,
			'length' => 60,
			'width'  => 35,
			'height' => 29
		),
		'CARTON_BOX_20KG'  => array(
			'name'   => __( 'Box 20 KG', 'woocommerce-edostavka' ),
			'weight' => 20,
			'length' => 47,
			'width'  => 40,
			'height' => 43
		),
		'CARTON_BOX_30KG'  => array(
			'name'   => __( 'Box 30 KG', 'woocommerce-edostavka' ),
			'weight' => 30,
			'length' => 69,
			'width'  => 39,
			'height' => 42
		)
	) );
}

function wc_edostavka_get_order_statuses() {
	return apply_filters( 'wc_edostavka_order_statuses', array(
		'NEW'                                    => _x( 'New', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED'                               => _x( 'Accepted', 'Cdek order status', 'woocommerce-edostavka' ),
		'CREATED'                                => _x( 'Created', 'Cdek order status', 'woocommerce-edostavka' ),
		'DELIVERED'                              => _x( 'Delivered', 'Cdek order status', 'woocommerce-edostavka' ),
		'NOT_DELIVERED'                          => _x( 'Not delivered', 'Cdek order status', 'woocommerce-edostavka' ),
		'CANCELED'                               => _x( 'Canceled by manager', 'Cdek order status', 'woocommerce-edostavka' ),
		'INVALID'                                => _x( 'Incorrect order', 'Cdek order status', 'woocommerce-edostavka' ),
		'RECEIVED_AT_SHIPMENT_WAREHOUSE'         => _x( 'Accepted by the sender\'s warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'RECEIVED_AT_SENDER_WAREHOUSE'           => _x( 'Accepted by the sender\'s warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'READY_FOR_SHIPMENT_IN_SENDER_CITY'      => _x( 'Issued for delivery in the sender\'s city', 'Cdek order status', 'woocommerce-edostavka' ),
		'RETURNED_TO_SENDER_CITY_WAREHOUSE'      => _x( 'Returned to the sender\'s warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'TAKEN_BY_TRANSPORTER_FROM_SENDER_CITY'  => _x( 'Handed over to the carrier in the sender\'s city', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED_IN_SENDER_CITY'                => _x( 'Received in the sending city', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED_IN_RECIPIENT_CITY'             => _x( 'Received in destination city', 'Cdek order status', 'woocommerce-edostavka' ),
		'SENT_TO_TRANSIT_CITY'                   => _x( 'Shipped to the transit city', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED_IN_TRANSIT_CITY'               => _x( 'Received in the transit city', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED_AT_TRANSIT_WAREHOUSE'          => _x( 'Accepted by the transit warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'RETURNED_TO_TRANSIT_WAREHOUSE'          => _x( 'Returned to the transit warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'READY_FOR_SHIPMENT_IN_TRANSIT_CITY'     => _x( 'Issued for delivery in the transit city', 'Cdek order status', 'woocommerce-edostavka' ),
		'TAKEN_BY_TRANSPORTER_FROM_TRANSIT_CITY' => _x( 'Handed over to the carrier in the transit city', 'Cdek order status', 'woocommerce-edostavka' ),
		'SENT_TO_SENDER_CITY'                    => _x( 'Sent to the sender city', 'Cdek order status', 'woocommerce-edostavka' ),
		'SENT_TO_RECIPIENT_CITY'                 => _x( 'Shipped to the receiver\'s city', 'Cdek order status', 'woocommerce-edostavka' ),
		'ARRIVED_AT_RECIPIENT_CITY'              => _x( 'Received in the receiver\'s city', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED_AT_RECIPIENT_CITY_WAREHOUSE'   => _x( 'Accepted by the delivery warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'ACCEPTED_AT_PICK_UP_POINT'              => _x( 'Accepted by the warehouse for pickup', 'Cdek order status', 'woocommerce-edostavka' ),
		'TAKEN_BY_COURIER'                       => _x( 'Issued for delivery', 'Cdek order status', 'woocommerce-edostavka' ),
		'RETURNED_TO_RECIPIENT_CITY_WAREHOUSE'   => _x( 'Returned to the delivery warehouse', 'Cdek order status', 'woocommerce-edostavka' ),
		'IN_CUSTOMS_INTERNATIONAL'               => _x( 'Customs clearance in the country of departure', 'Cdek order status', 'woocommerce-edostavka' ),
		'SHIPPED_TO_DESTINATION'                 => _x( 'Sent to destination country', 'Cdek order status', 'woocommerce-edostavka' ),
		'PASSED_TO_TRANSIT_CARRIER'              => _x( 'Handed over to transit carrier', 'Cdek order status', 'woocommerce-edostavka' ),
		'IN_CUSTOMS_LOCAL'                       => _x( 'Customs clearance in the country of destination', 'Cdek order status', 'woocommerce-edostavka' ),
		'CUSTOMS_COMPLETE'                       => _x( 'Customs clearance completed', 'Cdek order status', 'woocommerce-edostavka' ),
		'POSTOMAT_POSTED'                        => _x( 'Laid in postamat', 'Cdek order status', 'woocommerce-edostavka' ),
		'POSTOMAT_SEIZED'                        => _x( 'Withdrawn from the post office by courier', 'Cdek order status', 'woocommerce-edostavka' ),
		'POSTOMAT_RECEIVED'                      => _x( 'Withdrawn from the post office by the client', 'Cdek order status', 'woocommerce-edostavka' ),
	) );
}

/**
 * Returns array of keys to available for showing
 *
 * @return array
 */
function wc_edostavka_get_status_keys_for_show() {
	return apply_filters( 'wc_edostavka_status_keys_for_show', array(
		'ACCEPTED',
		'CREATED',
		'DELIVERED',
		'NOT_DELIVERED',
		'INVALID',
		'POSTOMAT_RECEIVED',
		'TAKEN_BY_COURIER',
		'RECEIVED_AT_SHIPMENT_WAREHOUSE',
		'SENT_TO_RECIPIENT_CITY',
		'ACCEPTED_AT_RECIPIENT_CITY_WAREHOUSE',
		'ACCEPTED_IN_RECIPIENT_CITY'
	) );
}

/**
 * Return boolean true or false depending on Dadata balance more than or equal zero
 *
 * @param bool $force_disable Set 'no' value to 'enable_suggestions_address' option if balance equals zero.
 *
 * @return bool
 */
function wc_edostavka_is_dadata_available( $force_disable = false ) {

	$balance = wc_edostavka_get_dadata_balance( $force_disable );

	if ( $balance > 0 ) {
		return true;
	} elseif ( $force_disable ) {
		wc_edostavka_shipping()->get_integration_handler()->update_option( 'enable_suggestions_state', 'no' );
		wc_edostavka_shipping()->get_integration_handler()->update_option( 'enable_suggestions_city', 'no' );
		wc_edostavka_shipping()->get_integration_handler()->update_option( 'enable_suggestions_address', 'no' );
		wc_edostavka_shipping()->get_integration_handler()->update_option( 'enable_detect_customer_location', 'no' );
	}

	return false;
}

/**
 * Resets customer chosen delivery point data from session
 *
 * @return void
 */
function wc_edostavka_reset_delivery_point_data() {
	if ( WC()->session ) {
		WC()->session->set( 'chosen_delivery_point', array() );
	}
}

/**
 * Sets customer chosen delivery point
 *
 * @param array $data Array of data
 *
 * @return array|null
 * @throws Woodev_Plugin_Exception
 */
function wc_edostavka_set_delivery_point_data( $data = array() ) {

	if ( WC()->session && ! empty( $data ) ) {

		if ( ! empty( $data['code'] ) && ! empty( $data['type'] ) ) {

			$chosen_delivery_point = ( array ) WC()->session->get( 'chosen_delivery_point', array() );

			$chosen_delivery_point[ $data['location']['city_code'] ][ strtolower( $data['type'] ) ] = $data;

			WC()->session->set( 'chosen_delivery_point', $chosen_delivery_point );

			return $chosen_delivery_point;

		} else {
			throw new Woodev_Plugin_Exception( 'Не переданы обязательные парметры.' );
		}

	} else {
		throw new Woodev_Plugin_Exception( 'Свойство session класса WC не инициирован или объект данных пуст.' );
	}
}

/**
 * Returns chosen customer delivery point by city ID and delivery type
 *
 * @param numeric-string $city_id City ID by CDEK base
 * @param string $type Must be pvz or postamat
 *
 * @return false|mixed
 */
function wc_edostavka_get_delivery_point_data( $city_id, $type = '' ) {

	if ( WC()->session && $city_id ) {
		$chosen_delivery_point = WC()->session->get( 'chosen_delivery_point', array() );
		if ( isset( $chosen_delivery_point[ $city_id ] ) ) {
			if ( ! empty( $type ) && in_array( $type, array(
					'pvz',
					'postamat'
				), true ) && isset( $chosen_delivery_point[ $city_id ][ $type ] ) ) {
				return $chosen_delivery_point[ $city_id ][ $type ];
			} elseif ( empty( $type ) ) {
				return $chosen_delivery_point[ $city_id ];
			}
		}
	}

	return false;
}

/**
 * @return WC_Edostavka_Box_Packer
 */
function wc_edostavka_get_package_box_handler( $packing_method = null ) {

	$name_item_box   = wc_edostavka_shipping()->get_integration_handler()->get_option( 'name_item_box', _x( 'Box', 'Name of item box', 'woocommerce-edostavka' ) );
	$name_single_box = wc_edostavka_shipping()->get_integration_handler()->get_option( 'name_single_box', __( 'Common box', 'woocommerce-edostavka' ) );
	$boxes           = wc_edostavka_shipping()->get_integration_handler()->get_option( 'boxes', array() );

	if ( is_null( $packing_method ) && ! in_array( $packing_method, array_keys( wc_edostavka_get_box_packing_methods() ), true ) ) {
		$packing_method = wc_edostavka_shipping()->get_integration_handler()->get_option( 'packing_method', 'per_item' );
	}

	switch ( $packing_method ) {
		case 'box_packing' :

			$packer = new WC_Edostavka_Box_Packer( new Woodev_Packer_Boxes() );

			switch ( wc_edostavka_shipping()->get_integration_handler()->get_option( 'unpacking_item_method', 'per_item' ) ) {
				case 'single_box' :
					$packer->set_cannot_pack_method( new Woodev_Packer_Single_Box( $name_single_box ) );
					break;
				case 'per_item':
				default:
					$packer->set_cannot_pack_method( new Woodev_Packer_Separately( $name_item_box ) );
					break;
			}

			$default_boxes = wc_edostavka_get_carton_boxes();

			foreach ( ( array ) $boxes as $box_key => $box ) {
				if ( ! isset( $box['enabled'] ) || ! $box['enabled'] ) {
					continue;
				}

				if ( in_array( $box_key, array_keys( $default_boxes ), true ) ) {
					$box_id = $box_key;
					$cost   = $box['cost'];
					$box    = $default_boxes[ $box_key ];
				} else {
					$box_id = Woodev_Helper::str_convert( $box['name'] );
					$cost   = $box['cost'];
				}

				$new_box = new Woodev_Packer_Box_Implementation(
					$box['length'],
					$box['width'],
					$box['height'],
					0,
					wc_get_weight( $box['weight'], 'g', 'kg' ),
					$box_id,
					$box['name'],
					array( 'cost' => $cost )
				);

				$packer->add_box( $new_box );
			}

			break;

		case 'single_box' :
			$packer = new WC_Edostavka_Box_Packer( new Woodev_Packer_Single_Box( $name_single_box ) );
			break;

		case 'per_item' :
		default :
			$packer = new WC_Edostavka_Box_Packer( new Woodev_Packer_Separately( $name_item_box ) );
			break;
	}

	return $packer;
}

/**
 * @param WC_Order $order
 * @param string $tracking_code
 *
 * @return void
 */
function wc_edostavka_trigger_tracking_code_email( WC_Order $order, $tracking_code ) {

	/** @var WC_CDEK_Tracking_Email $tracking_email */
	$tracking_email = WC()->mailer()->emails['WC_CDEK_Tracking_Email'];
	$tracking_email->trigger( $order->get_id(), $order, $tracking_code );
}

/**
 * @param WC_Order $order
 *
 * @return array|mixed|string
 */
function wc_edostavka_get_tracking_code( WC_Order $order ) {

	if ( ! is_a( $order, 'WC_Edostavka_Shipping_Order' ) ) {
		$order = new WC_Edostavka_Shipping_Order( $order );
	}

	return $order->get_order_meta( 'tracking_code' );
}

/**
 * @param WC_Order $order
 * @param string $tracking_code
 * @param boolean $remove
 *
 * @return bool
 */
function wc_edostavka_update_tracking_code( WC_Order $order, $tracking_code, $remove = false ) {

	$tracking_code = sanitize_text_field( $tracking_code );

	if ( ! is_a( $order, 'WC_Edostavka_Shipping_Order' ) ) {
		$order = new WC_Edostavka_Shipping_Order( $order );
	}

	if ( ! $remove && ! empty( $tracking_code ) ) {

		$order->update_order_meta( 'tracking_code', $tracking_code );

		$order->add_order_note( sprintf( '%s: %s', __( 'Tracking code added', 'woocommerce-edostavka' ), $tracking_code ) );

		wc_edostavka_trigger_tracking_code_email( $order, $tracking_code );

		do_action( 'wc_edostavka_update_tracking_code', $tracking_code, $order );

		return true;

	} elseif ( $remove && ( $tracking_code = $order->get_order_meta( 'tracking_code' ) ) ) {

		$order->update_order_meta( 'tracking_code', null );

		$order->add_order_note( sprintf( __( 'Tracking code deleted: %s', 'woocommerce-edostavka' ), $tracking_code ) );

		do_action( 'wc_edostavka_delete_tracking_code', $order->get_id() );

		return true;
	}

	return false;
}

function wc_edostavka_get_counts_order_status() {
	global $wpdb;

	$query = "
				SELECT {$wpdb->postmeta}.meta_value AS status, COUNT(*) AS count
				FROM {$wpdb->posts}
				INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)
				WHERE {$wpdb->posts}.post_type = 'shop_order'
				AND ({$wpdb->postmeta}.meta_key = '_wc_edostavka_status')
				AND {$wpdb->posts}.post_status NOT IN ( 'auto-draft', 'draft', 'trash' )
				GROUP BY {$wpdb->postmeta}.meta_value";

	return $wpdb->get_results( $query );
}

function wc_edostavka_update_all_orders() {

	$query = new WP_Query( apply_filters( 'wc_edostavka_update_orders_query_args', array(
		'post_type'   => wc_get_order_types( 'view-orders' ),
		'post_status' => array_keys( wc_get_order_statuses() ),
		'nopaging'    => true,
		'meta_query'  => array(
			array(
				'key'     => '_wc_edostavka_shipping',
				'compare' => 'EXISTS'
			),
			array(
				'key'     => '_wc_edostavka_cdek_order_id',
				'compare' => 'EXISTS'
			),
			array(
				'key'     => '_wc_edostavka_status',
				'value'   => array( 'NEW', 'DELIVERED', 'NOT_DELIVERED', 'CANCELED' ),
				'compare' => 'NOT IN'
			),
		)
	) ) );

	$query->set( 'fields', 'ids' );

	foreach ( $query->get_posts() as $order_id ) {

		$order = new WC_Edostavka_Shipping_Order( $order_id );

		$order->update_order();
	}

	if ( $query->post_count ) {
		wc_edostavka_shipping()->log( sprintf( _n( 'Updated information for %d order.', 'Updated information for %d orders.', $query->post_count, 'woocommerce-edostavka' ), $query->post_count ) );
	}

	return $query->post_count;
}

/**
 * @return bool
 */
function wc_edostavka_has_fee_payments() {
	return ( boolean ) count( array_filter( ( array ) get_option( WC_CDEK_SHIPPING_FEE_PAYMENT_KEY, array() ) ) ) > 0;
}

function wc_edostavka_api_get_access_token( $use_cache = false ) {

	$access_token = get_transient( WC_CDEK_SHIPPING_ACEESS_TOKEN_TRANSIENT_NAME );

	if ( false !== $access_token && ! $use_cache ) {
		return $access_token;
	}

	$settings = wp_parse_args( get_option( 'woocommerce_edostavka_settings', array() ), array(
		'api_login'    => '',
		'api_password' => ''
	) );

	try {

		$auth_api = new WD_Edostavka_Shipping_Auth_API();
		$response = $auth_api->get_token( $settings['api_login'], $settings['api_password'] )->get_token();

		if ( $response && ! empty( $response['access_token'] ) && ! empty( $response['expires_in'] ) ) {

			set_transient( WC_CDEK_SHIPPING_ACEESS_TOKEN_TRANSIENT_NAME, $response['access_token'], $response['expires_in'] );

			return $response['access_token'];
		}

	} catch ( Woodev_API_Exception $error ) {
		wc_edostavka_shipping()->log( $error->getMessage() );
	}

	return false;
}

/**
 * @param array $args {
 *
 * @type string $delivery_type Type of delivery method
 * @type string|integer $city_id ID of needed city
 * @type string $chose_btn_text Default button text
 * @type string $chosen_btn_text Button text if delivery point is already selected
 * @type boolean $is_chosen Flag is delivery point is already selected
 * @type array $classes Button additional classes array
 * @type boolean $return Flag, display or return button HTML
 * }
 *
 * @return string|void Prints HTML
 */
function wc_edostavka_generate_delivery_point_button( $args = array() ) {

	$args = apply_filters( 'wc_edostavka_generate_delivery_point_button_args', wp_parse_args( $args, array(
		'delivery_type'   => null,
		'city_id'         => null,
		'chose_btn_text'  => __( 'Choose point', 'woocommerce-edostavka' ),
		'chosen_btn_text' => __( 'Choose another point', 'woocommerce-edostavka' ),
		'is_chosen'       => false,
		'classes'         => array(),
		'return'          => true,
		'use_css'         => true
	) ) );

	$button_text    = $args['chose_btn_text'];
	$button_classes = array( 'button', 'wc-edostavka-choose-delivery-point' );
	$out            = __return_empty_string();

	if ( $args['use_css'] ) {
		ob_start();
		wc_edostavka_shipping()->load_template( 'views/delivery-map-styles.php' );
		$css = ob_get_clean();
	}


	if ( $args['is_chosen'] ) {
		$button_classes[] = 'wc-edostavka-choose-delivery-point--chosen';
		$button_text      = $args['chosen_btn_text'];
	}

	if ( ! empty( $args['classes'] ) && is_array( $args['classes'] ) ) {
		$button_classes = array_merge( $button_classes, $args['classes'] );
	}

	$out .= sprintf( '<button id="wc_edostavka_delivery_point_%s_%d" class="%s" data-delivery_type="%s" data-city_id="%s">%s</button>', $args['delivery_type'], $args['city_id'], implode( ' ', $button_classes ), $args['delivery_type'], $args['city_id'], $button_text );

	if ( $args['use_css'] ) {
		$out = '<style type="text/css">' . $css . '</style>' . $out;
	}

	if ( $args['return'] ) {
		return $out;
	} else {
		echo $out;
	}
}

function wc_edostavka_get_box_packing_methods() {
	return array(
		'per_item'    => 'Каждый товар индивидуально (по умолчанию)',
		'single_box'  => 'Упаковывать все товары в одну коробку',
		'box_packing' => 'Упаковывать в коробки с заданными размерами'
	);
}

/**
 * @throws Exception
 * @deprecated 2.2.0
 */
function wc_edostavka_get_customer_state_id() {
	wc_deprecated_function( __FUNCTION__, '2.2.0', 'wc_edostavka_get_customer_location()' );

	return wc_edostavka_shipping()->get_customer_handler()->get_city_code();
}

/**
 * @param numeric-string $code Code of city by CDEK base
 *
 * @throws Exception
 * @deprecated 2.2.0
 */
function wc_edostavka_set_customer_state_id( $code ) {
	wc_deprecated_function( __FUNCTION__, '2.2.0', 'wc_edostavka_set_customer_location()' );
	wc_edostavka_shipping()->get_customer_handler()->set_city_code( $code );
}

/**
 * @param array $atts
 *
 * @return void
 * @deprecated 2.2.0
 */
function wc_edostavka_create_map( $atts = array() ) {
	wc_deprecated_function( __FUNCTION__, '2.2.0' );
}
