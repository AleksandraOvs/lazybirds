<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Customer_Location {

	/**
	 * Customer location data.
	 *
	 * @var string[]
	 */
	protected $data = array(
		'country_code'  => null,
		'region_code'   => null,
		'region'        => null,
		'city_code'     => null,
		'city'          => null,
		'longitude'     => null,
		'latitude'      => null
	);

	/**
	 * Dirty when the data needs saving.
	 *
	 * @var bool $_dirty When something changes
	 */
	protected $_dirty = false;

	private $meta_name = 'wc_edostavka_customer_location';

	private $customer = null;

	public function __construct() {

		if( ! WC()->session ) return;

		if ( is_null( WC()->customer ) || ! WC()->customer instanceof WC_Customer ) {
			$wc_customer = new WC_Customer( get_current_user_id(), true );
		} else {
			$wc_customer = WC()->customer;
		}

		$customer_id    = $wc_customer->get_id();

		if( $customer_id > 0 ) {
			$this->customer = new WC_Customer( $customer_id );
		}

		//$this->data = array_replace_recursive( $this->data, $this->get_meta() );



	}

	public function init() {

		$this->data = array_replace_recursive( $this->data, $this->get_meta() );

		if ( is_user_logged_in() && ! is_null( $this->customer ) && get_current_user_id() !== $this->customer->get_id() ) {
			$this->_dirty = true;
			$this->save();
		}

		add_action( 'woocommerce_init', array( $this, 'init_session' ) );
		add_action( 'shutdown', array( $this, 'save' ), 35 );
	}

	public function init_session() {
		if ( is_user_logged_in() || is_admin() ) return;

		if( WC()->session && ! WC()->session->has_session() && is_null( $this->customer ) ) {
			WC()->session->set_customer_session_cookie( true );
		}
	}

	/**
	 * @return array
	 */
	private function get_meta() {

		$session_meta = WC()->session->get( $this->meta_name, array() );
		$customer_meta = $this->customer ? $this->customer->get_meta( $this->meta_name ) : array();

		return is_array( $customer_meta ) && ! empty( $customer_meta ) ? $customer_meta : $session_meta;
	}

	/**
	 * @return string[]
	 */
	public function get_location() {
		return $this->data;
	}

	/**
	 * @param array $location_data
	 *
	 * @return void
	 */
	public function set_location( $location_data = array() ) {

		foreach ( array_keys( $this->data ) as $prop ) {
			if( isset( $location_data[ $prop ] ) && is_callable( array( $this, "set_{$prop}" ) ) ) {
				$this->{"set_{$prop}"}( $location_data[ $prop ] );
			}
		}
	}

	public function save() {

		if( $this->_dirty ) {

			WC()->session->set( $this->meta_name, $this->data );

			if( $this->customer && $this->customer->get_id() > 0 ) {
				$this->customer->update_meta_data( $this->meta_name, $this->data );
				$this->customer->save_meta_data();
			}

			//Reset all data values
			$this->data = array_fill_keys( array_keys( $this->data ), null );
			$this->_dirty = false;
		}
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param  string $prop Name of prop to get.
	 * @return string|null
	 */
	protected function get_prop( $prop ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = $this->data[ $prop ];
		}

		return $value;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) && $value !== $this->get_prop( $prop ) ) {
			$this->data[ $prop ]    = $value;
			$this->_dirty           = true;
		}
	}

	/**
	 * Get customer country code
	 *
	 * @return string|null
	 */
	public function get_country_code() {
		return $this->get_prop( 'country_code' );
	}

	/**
	 * Get customer region code
	 *
	 * @return string|null
	 */
	public function get_region_code() {
		return $this->get_prop( 'region_code' );
	}

	/**
	 * Get customer region name
	 *
	 * @return string|null
	 */
	public function get_region() {
		return $this->get_prop( 'region' );
	}

	/**
	 * Get customer city code
	 *
	 * @return string|null
	 */
	public function get_city_code() {
		return $this->get_prop( 'city_code' );
	}

	/**
	 * Get customer city name
	 *
	 * @return string|null
	 */
	public function get_city() {
		return $this->get_prop( 'city' );
	}

	/**
	 * Get customer longitude
	 *
	 * @return string|null
	 */
	public function get_longitude() {
		return $this->get_prop( 'longitude' );
	}

	/**
	 * Get customer latitude
	 *
	 * @return string|null
	 */
	public function get_latitude() {
		return $this->get_prop( 'latitude' );
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
}
