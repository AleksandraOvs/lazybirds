<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return array of regions by params
 * @param $params array
 *
 * @return false|array
 */
function wc_edostavka_get_location_regions( $params = array() ) {

	$transient_name = sprintf( 'wc_edostavka_get_location_regions_%s', md5( serialize( $params ) ) );
	$regions = get_transient( $transient_name );

	if( false === $regions ) {

		try {

			$regions = wc_edostavka_shipping()->get_api()->get_location_regions( $params )->get_response_data();

			if( $regions ) {
				set_transient( $transient_name, $regions, DAY_IN_SECONDS * 60 );
			}

		} catch( Woodev_API_Exception $error ) {
			wc_edostavka_shipping()->log( $error->getMessage() );
		}
	}

	return $regions;
}

/**
 * Return array of cities by params
 *
 * @param array $params Request params {
 *     Array of request parameters.
 *     @see https://api-docs.cdek.ru/33829437.html See all available parameters
 *
 *     @type string $country_code   ISO code of country.
 *     @type int    $region_code    Region code.
 *     @type string $region         Name of region.
 *     @type int    $code           City code.
 *     @type string $city           Name of city
 *     @type float  $longitude      Longitude
 *     @type float  $latitude       Latitude
 * }
 *
 * @return false|array
 */
function wc_edostavka_get_location_cities( $params = array() ) {

	$transient_name = sprintf( 'wc_edostavka_get_location_cities_%s', md5( serialize( $params ) ) );
	$location = get_transient( $transient_name );

	if( false === $location ) {

		if( apply_filters( 'wc_edostavka_allow_legacy_location_cities_search', true, $params ) && empty( $params['code'] ) && ! empty( $params['city'] ) && ( ! isset( $params['lang'] ) || 'eng' !== $params['lang'] ) ) {

			$location = wc_edostavka_get_cities_legacy( $params['city'], $params['country_codes'], 25 );

			if( $location ) {
				return $location;
			}
		}

		try {

			$location = wc_edostavka_shipping()->get_api()->get_location_cities( $params )->get_response_data();

			set_transient( $transient_name, $location, WEEK_IN_SECONDS );

		} catch( Woodev_API_Exception $error ) {
			wc_edostavka_shipping()->log( $error->getMessage() );
		}
	}

	if( apply_filters( 'wc_edostavka_location_cities_sort_results', true, $params ) && is_array( $location ) ) {
		usort( $location, function ( $a, $b ) {
			return ( intval( $a->code ) > intval( $b->code ) ) ? 1 : -1;
		} );
	}

	return $location;
}


/**
 * Return array of cities by query string. This function uses legacy API
 *
 * - $query         = 'москва'
 * - $country_code  = 'RU'
 *
 * @param string    $query          Query string
 * @param string    $country_code   Code of country
 * @param int       $limit          Results limit
 *
 * @return array
 */
function wc_edostavka_get_cities_legacy( $query, $country_code = 'RU', $limit = 20 ) {

	$cities = array();

	try {

		$query_url = add_query_arg( array(
			'q'                 => wc_clean( $query ),
			'countryIsoList'    => $country_code,
			'limit'             => intval( $limit ),
			'displayPostCode'   => false
		), esc_url( 'https://api.cdek.ru/city/getListByTerm/json.php' ) );

		$response = wp_safe_remote_get( $query_url );

		if ( ! is_wp_error( $response ) ) {

			$result = json_decode( wp_remote_retrieve_body( $response ), true );

			if( ! empty( $result['geonames'] ) && is_array( $result['geonames'] ) ) foreach( $result['geonames'] as $geo ) {

				if( empty( $geo['id'] ) || in_array( $geo['id'], wc_edostavka_get_disallow_cities_ids() ) ) continue;

				$city_entity = new stdClass();

				$city_entity->code          = $geo['id'];
				$city_entity->city          = $geo['cityName'];
				$city_entity->region_code   = $geo['regionId'];
				$city_entity->region        = isset( $geo['regionName'] ) ? $geo['regionName'] : '';
				$city_entity->country_code  = $geo['countryIso'];
				$city_entity->longitude		= null;
				$city_entity->latitude		= null;

				$cities[] = $city_entity;
			}
		} else {
			throw new Exception( $response->get_error_message() );
		}

	} catch ( Exception $exception ) {
		wc_edostavka_shipping()->log( $exception->getMessage() );
	}

	return $cities;
}

function wc_edostavka_get_location_postcodes( $code ) {

	$postcodes = array();

	try {

		$postcodes = wc_edostavka_shipping()->get_api()->get_location_postcodes( $code );

	} catch ( Exception $e ) {
		wc_edostavka_shipping()->log( sprintf( 'При получении списка индексов для ID населённого пункта произошла ошибка: %s', $e->getMessage() ) );
	}

	return $postcodes;
}

function wc_edostavka_get_deliverypoints( $params = array() ) {

	$transient_name = sprintf( 'wc_edostavka_get_deliverypoints_%s', md5( serialize( $params ) ) );
	$delivery_points = get_transient( $transient_name );

	if( false === $delivery_points ) {

		try {

			$delivery_points = wc_edostavka_shipping()->get_api()->get_deliverypoints( $params )->get_response_data();

			set_transient( $transient_name, $delivery_points, WEEK_IN_SECONDS );

		} catch( Woodev_API_Exception $error ) {
			wc_edostavka_shipping()->log( $error->getMessage() );
		}
	}

	return $delivery_points;
}

/**
 * @return int | null
 */
function wc_edostavka_get_dadata_balance() {

	$token = wc_edostavka_shipping()->get_integration_handler()->get_option( 'dadata_token' );
	$secret = wc_edostavka_shipping()->get_integration_handler()->get_option( 'dadata_secret' );

	if( ! empty( $token ) && ! empty( $secret ) ) {

		$transient_name = sprintf( 'wc_edostavka_dadata_balance_%s', md5( serialize( array( $token, $secret ) ) ) );
		$balance = get_transient( $transient_name );

		if( false === $balance ) {

			try {

				$data_api = new WC_Edostavka_Dadata_API( $token, $secret );
				$response_balance = $data_api->get_balance();

				$balance = $response_balance->get_balance();
				set_transient( $transient_name, $balance, 12 * HOUR_IN_SECONDS );

			} catch ( Woodev_API_Exception $e ) {
				wc_edostavka_shipping()->log( $e->getMessage() );
			}
		}

		return $balance;
	}

	return null;

}

function wc_edostavka_get_customer_geo_location() {

	$customer_locations = array();

	try {

		$dadata_token   = wc_clean( wc_edostavka_shipping()->get_integration_handler()->get_option( 'dadata_token' ) );
		$dadata_secret  = wc_clean( wc_edostavka_shipping()->get_integration_handler()->get_option( 'dadata_secret' ) );
		$dadata_api     = new WC_Edostavka_Dadata_API( $dadata_token, $dadata_secret );
		$user_locale    = wc_edostavka_get_locale();

		$geo_location_params = array(
			'ip'        => WC_Geolocation::get_ip_address(),
			'language'  => substr( $user_locale, 0, 2 )
		);

		/** @var WD_Edostavka_Dadata_API_Response|Woodev_API_Exception $customer_ip_location */
		$customer_ip_location = $dadata_api->get_ip_local_address( $geo_location_params );

		$customer_geo_location = $customer_ip_location->get_location();

		if( ! empty( $customer_geo_location ) && ! empty( $customer_geo_location->data ) ) {

			$kladr_id = $customer_geo_location->data->kladr_id;

			if( ! empty( $customer_geo_location->data->postal_code ) ) {
				$customer_locations['postcode'] = $customer_geo_location->data->postal_code;
			}

			if( isset( $kladr_id ) && ! empty( $kladr_id ) ) {

				/** @var WD_Edostavka_Dadata_API_Response $suggestions_data */
				$suggestions_data = $dadata_api->get_delivery_id( $kladr_id );
				$suggestions = $suggestions_data->get_suggestions();

				if( ! empty( $suggestions ) && is_array( $suggestions ) ) {

					$first_suggestion = array_shift( $suggestions );

					if( isset( $first_suggestion->data, $first_suggestion->data->cdek_id ) && ! empty( $first_suggestion->data->cdek_id ) ) {

						$location_city_params = array(
							'code'          => $first_suggestion->data->cdek_id,
							'size'          => 1,
							'lang'          => $user_locale
						);

						if( isset( $customer_geo_location->data->country_iso_code ) ) {
							$location_city_params['country_codes'] = $customer_geo_location->data->country_iso_code;
						}

						$cities = wc_edostavka_get_location_cities( $location_city_params );

						if( ! empty( $cities ) && is_array( $cities ) ) {

							$first_city = array_shift( $cities );

							$customer_locations = array(
								'country_code'  => $first_city->country_code,
								'region_code'   => $first_city->region_code,
								'region'        => $first_city->region,
								'city_code'     => $first_city->code,
								'city'          => $first_city->city,
								'longitude'     => $first_city->longitude,
								'latitude'      => $first_city->latitude
							);
						}
					}
				}
			}
		}

	} catch ( Woodev_API_Exception $e ) {
		wc_edostavka_shipping()->log( $e->getMessage() );
	}

	return $customer_locations;
}
