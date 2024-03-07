<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_Auth_Request extends Woodev_API_JSON_Request {

	private $client_id;

	private $client_secret;

	public function __construct( $client_id, $client_secret ) {
		$this->client_id 		= $client_id;
		$this->client_secret 	= $client_secret;
	}

	public function get_auth_token() {
		$this->path = '/oauth/token';
		$this->method = 'POST';
		$this->params = array(
			'grant_type'	=> 'client_credentials',
			'client_id'		=> $this->client_id,
			'client_secret'	=> $this->client_secret
		);
	}

	public function to_string() {
		return http_build_query( $this->get_params() );
	}
}
