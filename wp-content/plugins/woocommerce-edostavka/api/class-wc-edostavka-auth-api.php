<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_Auth_API extends Woodev_API_Base {

	public function __construct() {

		$this->set_request_content_type_header( 'application/x-www-form-urlencoded' );
		$this->set_request_accept_header( 'application/json' );
		$this->set_response_handler( 'WD_Edostavka_Shipping_API_Response' );

		$this->request_uri = 'https://api.cdek.ru/v2';
	}

	public function get_token( $client_id = '', $client_secret = '' ) {

		$request = $this->get_new_request( array(
			'client_id'		=> $client_id,
			'client_secret'	=> $client_secret
		) );
		$request->get_auth_token();

		return $this->perform_request( $request );
	}

	protected function do_post_parse_response_validation() {
		$response	= $this->get_response();
		$request	= $this->get_request();
		$errors		= $response->get_errors();

		if( $errors ) {
			throw new Woodev_API_Exception( implode( ".\n", $errors ) );
		}
	}

	protected function get_new_request( $args = array() ) {
		return new WD_Edostavka_Shipping_Auth_Request( $args[ 'client_id' ], $args[ 'client_secret' ] );
	}

	protected function get_plugin() {
		return wc_edostavka_shipping();
	}
}
