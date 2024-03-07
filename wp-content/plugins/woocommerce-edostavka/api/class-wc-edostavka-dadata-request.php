<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Dadata_API_Request extends Woodev_API_JSON_Request {

	use Woodev_Cacheable_Request_Trait;

	public function get_balance() {
		$this->path = '/profile/balance';
		$this->method = 'GET';
	}

	public function get_stats() {
		$this->path = '/stat/daily';
		$this->method = 'GET';
	}

	public function get_ip_local_address( $params ) {
		$this->path = '/iplocate/address';
		$this->method = 'GET';
		$this->params = $params;
	}

	public function get_delivery_id( $kladr_id ) {
		$this->path = '/findById/delivery';
		$this->method = 'POST';
		$this->params = array( 'query' => $kladr_id );
	}

	public function get_path() {

		$path   = $this->path;
		$params = $this->get_params();

		if ( 'GET' === $this->get_method() && ! empty( $params ) ) {

			$path .= '?' . http_build_query( $this->get_params(), '', '&' );
		}

		return $path;
	}

	public function to_string() {

		if ( 'GET' === $this->get_method() ) {
			return array();
		} elseif( in_array( $this->get_method(), array( 'POST', 'PUT', 'DELETE' ), true ) ) {
			return wp_json_encode( $this->get_params() );
		} else {
			return http_build_query( $this->get_params() );
		}
	}
}
