<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * @property object $response_data
 * @property string $balance
 * @property object $location
 * @property array  $suggestions
 *
 */
class WD_Edostavka_Dadata_API_Response extends Woodev_API_JSON_Response {

	public function get_errors() {}

	public function get_response_data() {
		return $this->response_data;
	}

	public function get_balance() {
		return $this->balance;
	}

	public function get_location() {
		return $this->location;
	}

	public function get_suggestions() {
		return $this->suggestions;
	}
}
