<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_API extends Woodev_Cacheable_API_Base {

	public function __construct() {

		$this->set_request_content_type_header( 'application/json' );
		$this->set_request_accept_header( 'application/json' );
		$this->set_response_handler( 'WD_Edostavka_Shipping_API_Response' );
		$this->set_request_header( 'Authorization', sprintf( 'Bearer %s', wc_edostavka_api_get_access_token() ) );

		$this->request_uri = 'https://api.cdek.ru/v2';
	}

	public function calculate_tariff( $params = array() ) {
		$request = $this->get_new_request();
		$request->get_tariff( $params );
		return $this->perform_request( $request );
	}

	public function get_deliverypoints( $params = array() ) {
		$request = $this->get_new_request();
		$request->get_deliverypoints( $params );
		$request->set_cache_lifetime( WEEK_IN_SECONDS );
		return $this->perform_request( $request );
	}

	public function get_location_regions( $params = array() ) {
		$request = $this->get_new_request();
		$request->get_location_regions( $params );
		$request->set_cache_lifetime( MONTH_IN_SECONDS );
		return $this->perform_request( $request );
	}

	public function get_location_cities( $params = array() ) {
		$request = $this->get_new_request();
		$request->get_location_cities( $params );
		$request->set_cache_lifetime( MONTH_IN_SECONDS );
		return $this->perform_request( $request );
	}

	/**
	 * @param string $code Code ID of city for search
	 *
	 * @return array
	 * @throws Woodev_API_Exception
	 * @throws Woodev_Plugin_Exception
	 */
	public function get_location_postcodes( $code ) {

		$request = $this->get_new_request();
		$request->get_location_postcodes( $code );

		/** @var WD_Edostavka_Shipping_API_Response $response */
		$response = $this->perform_request( $request );

		return $response->get_postcodes();
	}

	public function create_order( $params = array() ) {
		$request = $this->get_new_request();
		$request->create_order( $params );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function get_order( $order_uuid, $params = array() ) {
		$request = $this->get_new_request();
		$request->get_order( $order_uuid, $params );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function update_order( $params = array() ) {
		$request = $this->get_new_request();
		$request->update_order( $params );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function remove_order( $order_uuid, $params = array() ) {
		$request = $this->get_new_request();
		$request->remove_order( $order_uuid, $params );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function refund_order( $order_uuid, $params = array() ) {
		$request = $this->get_new_request();
		$request->refund_order( $order_uuid, $params );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	/**
	 * @param string $type Type of webhook
	 * @param string $url URL of webhook
	 *
	 * @return string|void
	 * @throws Woodev_API_Exception
	 */
	public function create_webhook( $type, $url ) {

		$request = $this->get_new_request();
		$request->create_webhook( $type, $url );
		$request->bypass_cache();

		/** @var WD_Edostavka_Shipping_API_Response $response */
		$response = $this->perform_request( $request );

		return $response->get_webhook_id();
	}

	public function get_webhook( $id ) {
		$request = $this->get_new_request();
		$request->get_webhook( $id );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	/**
	 * @param string $id UUID of webhook
	 *
	 * @return object|Woodev_API_Request|Woodev_API_Response
	 * @throws Woodev_API_Exception
	 */
	public function delete_webhook( $id ) {
		$request = $this->get_new_request();
		$request->delete_webhook( $id );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	/**
	 * @param array $params
	 *
	 * @return object|Woodev_API_Request|Woodev_API_Response
	 * @throws Woodev_API_Exception
	 */
	public function create_call_courier( array $params ) {
		$request = $this->get_new_request();
		$request->call_courier( $params );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function get_order_waybill( $uuid ) {
		$request = $this->get_new_request();
		$request->get_order_waybill( $uuid );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function create_order_waybill( array $ids ) {
		$request = $this->get_new_request();
		$request->create_order_waybill( $ids );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function create_order_barcode( array $ids ) {
		$request = $this->get_new_request();
		$request->create_order_barcode( $ids );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	public function get_print_file( $uuid, $type ) {
		$request = $this->get_new_request();
		$request->get_print_file( $uuid, $type );
		$request->bypass_cache();
		return $this->perform_request( $request );
	}

	/**
	 * Validates the response after parsing.
	 *
	 * @since 2.2.0
	 * @throws Woodev_API_Exception
	 */
	protected function do_post_parse_response_validation() {
		$response	= $this->get_response();
		$errors		= $response->get_errors();

		if( $this->get_response_code() > 400 ) {
			throw new Woodev_API_Exception( $this->get_response_message(), $this->get_response_code() );
		} elseif( $errors ) {
			throw new Woodev_API_Exception( implode( ".\n", $errors ) );
		}
	}

	protected function get_request_user_agent() {
		return Woodev_Helper::str_convert( parent::get_request_user_agent() );
	}

	/**
	 * Build and return a new API request object.
	 *
	 * @since 2.2.0
	 * @see Woodev_API_Base::get_new_request()
	 * @param array $args
	 * @return WD_Edostavka_Shipping_API_Request the request object
	 */
	protected function get_new_request( $args = array() ) {
		return new WD_Edostavka_Shipping_API_Request();
	}

	/**
	 * Get the plugin class instance associated with this API.
	 *
	 * @since 2.2.0
	 * @return WC_Edostavka_Shipping
	 */
	protected function get_plugin() {
		return wc_edostavka_shipping();
	}
}
