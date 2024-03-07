<?php

defined( 'ABSPATH' ) || exit;

class WC_Edostavka_Customer_Location_Data extends WC_Data {

	/**
	 * Stores customer data.
	 *
	 * @var string[]
	 */
	protected $data = array(
		'country_code'     => null,
		'region_code'      => null,
		'region'           => null,
		'fias_region_guid' => null,
		'sub_region'       => null,
		'city_code'        => null,
		'city'             => null,
		'city_uuid'        => null,
		'fias_guid'        => null,
		'kladr_code'       => null,
		'longitude'        => null,
		'latitude'         => null,
		'time_zone'        => null
	);

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'wc_edostavka_customer_location';

	/**
	 * Stores meta in cache for future reads.
	 *
	 * A group must be set to enable caching.
	 * @var string
	 */
	protected $cache_group = 'customer_location';

	/**
	 * @param WC_Edostavka_Customer_Location_Data|int $customer Customer ID or data.
	 *
	 * @throws Exception If customer cannot be read/found and $data is set.
	 */
	public function __construct( $customer = 0 ) {
		parent::__construct( $customer );

		if ( $customer instanceof self ) {
			$this->set_id( absint( $customer->get_id() ) );
		} elseif ( is_numeric( $customer ) && $customer > 0 ) {
			$this->set_id( $customer );
		}

		$this->data_store = WC_Data_Store::load( 'customer-location' );

		$this->get_default_location();

		if ( $this->get_id() ) {
			$this->data_store->read( $this );
		} else {
			$this->set_id( 0 );
			$this->set_object_read( true );
		}

		if ( isset( WC()->session ) && ! $this->get_id() ) {
			$this->data_store = WC_Data_Store::load( 'customer-location-session' );
			$this->data_store->read( $this );
		}
	}

	public function get_object_type() {
		return $this->object_type;
	}

	public function allow_geo_location() {
		return apply_filters( 'wc_edostavka_customer_allow_geo_location', wc_string_to_bool( wc_edostavka_shipping()->get_integration_handler()->get_option( 'enable_detect_customer_location', 'no' ) ), $this );
	}

	public function default_city_code() {
		return apply_filters( 'wc_edostavka_customer_default_city_code', wc_edostavka_shipping()->get_integration_handler()->get_option( 'customer_default_city' ), $this );
	}

	/**
	 * @return string[]
	 */
	public function get_location( $context = 'view' ) {

		$location = array(
			'country_code'     => $this->get_country_code( $context ),
			'region_code'      => $this->get_region_code( $context ),
			'region'           => $this->get_region( $context ),
			'fias_region_guid' => $this->get_fias_region_guid( $context ),
			'sub_region'       => $this->get_sub_region( $context ),
			'city_code'        => $this->get_city_code( $context ),
			'city'             => $this->get_city( $context ),
			'city_uuid'        => $this->get_city_uuid( $context ),
			'fias_guid'        => $this->get_fias_guid( $context ),
			'kladr_code'       => $this->get_kladr_code( $context ),
			'longitude'        => $this->get_longitude( $context ),
			'latitude'         => $this->get_latitude( $context ),
			'time_zone'        => $this->get_time_zone( $context )
		);

		return apply_filters( 'wc_edostavka_customer_location_data', $location );
	}

	/**
	 * @param array $location_data
	 *
	 * @return void
	 */
	public function set_location( $location_data = array() ) {
		$this->set_props( $location_data );
	}

	/**
	 * Get customer country code
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_country_code( $context = 'view' ) {
		return $this->get_prop( 'country_code', $context );
	}

	/**
	 * Get customer region code
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_region_code( $context = 'view' ) {
		return $this->get_prop( 'region_code', $context );
	}

	/**
	 * Get customer region name
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_region( $context = 'view' ) {
		return $this->get_prop( 'region', $context );
	}

	/**
	 * Get customer region FIAS
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_fias_region_guid( $context = 'view' ) {
		return $this->get_prop( 'fias_region_guid', $context );
	}

	/**
	 * Get location sub-region
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_sub_region( $context = 'view' ) {
		return $this->get_prop( 'sub_region', $context );
	}

	/**
	 * Get customer city code
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_city_code( $context = 'view' ) {
		return $this->get_prop( 'city_code', $context );
	}

	/**
	 * Get customer city name
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_city( $context = 'view' ) {
		return $this->get_prop( 'city', $context );
	}

	/**
	 * Get customer city UUID by CDEK database
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_city_uuid( $context = 'view' ) {
		return $this->get_prop( 'city_uuid', $context );
	}

	/**
	 * Get customer city FIAS
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_fias_guid( $context = 'view' ) {
		return $this->get_prop( 'fias_guid', $context );
	}

	/**
	 * Get customer city KLADR code
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_kladr_code( $context = 'view' ) {
		return $this->get_prop( 'kladr_code', $context );
	}

	/**
	 * Get customer longitude
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_longitude( $context = 'view' ) {
		return $this->get_prop( 'longitude', $context );
	}

	/**
	 * Get customer latitude
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_latitude( $context = 'view' ) {
		return $this->get_prop( 'latitude', $context );
	}

	/**
	 * Get customer location time zone
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string|null
	 */
	public function get_time_zone( $context = 'view' ) {
		return $this->get_prop( 'time_zone', $context );
	}

	/**
	 * Set customer's country code.
	 *
	 * @param string $country_code Country code.
	 */
	public function set_country_code( $country_code ) {
		$this->set_prop( 'country_code', $country_code );
	}

	/**
	 * Set customer's region code.
	 *
	 * @param string $region_code Region code.
	 */
	public function set_region_code( $region_code ) {
		$this->set_prop( 'region_code', $region_code );
	}

	/**
	 * Set customer's region name.
	 *
	 * @param string $region_name Region name.
	 */
	public function set_region( $region_name ) {
		$this->set_prop( 'region', $region_name );
		if ( WC()->customer && ! is_null( $region_name ) ) {
			WC()->customer->set_billing_state( $region_name );
			WC()->customer->set_shipping_state( $region_name );
		}
	}

	/**
	 * Set location region FIAS.
	 *
	 * @param string $fias_guid Region FIAS code.
	 */
	public function set_fias_region_guid( $fias_guid ) {
		$this->set_prop( 'fias_region_guid', $fias_guid );
	}

	/**
	 * Set location sub-region.
	 *
	 * @param string $sub_region_name sub-region name.
	 */
	public function set_sub_region( $sub_region_name ) {
		$this->set_prop( 'sub_region', $sub_region_name );
	}

	/**
	 * Set customer's city code.
	 *
	 * @param string $city_code City code.
	 */
	public function set_city_code( $city_code ) {
		$this->set_prop( 'city_code', $city_code );
	}

	/**
	 * Set customer's city name
	 *
	 * @param string $city_name City name.
	 */
	public function set_city( $city_name ) {
		$this->set_prop( 'city', $city_name );
		if ( WC()->customer && ! is_null( $city_name ) ) {
			WC()->customer->set_billing_city( $city_name );
			WC()->customer->set_shipping_city( $city_name );
		}
	}

	/**
	 * Set customer's city KLADR code.
	 *
	 * @param string $kladr_code City KLADR code.
	 */
	public function set_kladr_code( $kladr_code ) {
		$this->set_prop( 'kladr_code', $kladr_code );
	}

	/**
	 * Set location city FIAS code.
	 *
	 * @param string $fias City FIAS code.
	 */
	public function set_fias_guid( $fias ) {
		$this->set_prop( 'fias_guid', $fias );
	}

	/**
	 * Set location city UUID.
	 *
	 * @param string $uuid City UUID code by CDEKs database.
	 */
	public function set_city_uuid( $uuid ) {
		$this->set_prop( 'city_uuid', $uuid );
	}

	/**
	 * Set customer's longitude
	 *
	 * @param string $longitude Longitude coordinate.
	 */
	public function set_longitude( $longitude ) {
		$this->set_prop( 'longitude', $longitude );
	}

	/**
	 * Set customer's latitude
	 *
	 * @param string $latitude Latitude coordinate.
	 */
	public function set_latitude( $latitude ) {
		$this->set_prop( 'latitude', $latitude );
	}

	/**
	 * Set location time zone
	 *
	 * @param string $time_zone Location time zone.
	 */
	public function set_time_zone( $time_zone ) {
		$this->set_prop( 'time_zone', $time_zone );
	}

	public function get_default_location() {

		if ( ! is_null( $this->default_city_code() ) && $this->default_city_code() > 0 ) {

			$city_location = wc_edostavka_get_location_cities( array(
				'code' => absint( $this->default_city_code() ),
				'size' => 1,
				'lang' => wc_edostavka_get_locale()
			) );

			if ( $city_location && $city_location[0] ) {

				$this->default_data = array(
					'country_code' => $city_location[0]->country_code,
					'region_code'  => $city_location[0]->region_code,
					'region'       => $city_location[0]->region,
					'city_code'    => $city_location[0]->code,
					'city'         => $city_location[0]->city,
					'longitude'    => $city_location[0]->longitude,
					'latitude'     => $city_location[0]->latitude
				);
			}
		}

		if ( empty( array_filter( $this->default_data ) ) && $this->allow_geo_location() ) {
			$customer_geo_location = wc_edostavka_get_customer_geo_location();
			if ( $customer_geo_location && isset( $customer_geo_location['city_code'] ) && ! empty( $customer_geo_location['city_code'] ) ) {
				$this->default_data = $customer_geo_location;
			}
		}

		return $this->default_data;
	}

}
