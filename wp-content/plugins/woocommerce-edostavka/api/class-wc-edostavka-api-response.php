<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * @property string         $error
 * @property string         $message
 * @property string         $error_description The error text description
 * @property string         $status The error status code
 * @property stdClass[]     $errors
 * @property string         $access_token
 * @property integer        $expires_in
 * @property stdClass[]     $requests
 *
 */

class WD_Edostavka_Shipping_API_Response extends Woodev_API_JSON_Response {

	/**
	 * Gets the API errors, if any.
	 *
	 * @since 2.2.0
	 * @return array
	 */
	public function get_errors() {

		$errors = array();

		if( ! empty( $this->error ) ) {

			$error_text = sprintf( 'Error: %s', WD_Edostavka_Shipping_API_Errors::get_error_text( $this->error ) );

			if( empty( $this->message ) ) {
				$error_text .= sprintf( '. Error message: %s', $this->message );
			} elseif( ! empty( $this->error_description ) ) {
				$error_text .= sprintf( '. Error message: %s', $this->error_description );
			}

			if( ! empty( $this->status ) ) {
				$error_text .= sprintf( '. Error status code: %s', $this->status );
			}

			$errors[] = $error_text;
		}

		if( $this->errors && is_array( $this->errors ) ) {
			foreach( $this->errors as $error ) {
				$errors[] = sprintf( 'Error: %s. Error message: %s.', WD_Edostavka_Shipping_API_Errors::get_error_text( $error->code ), $error->message );
			}
		}

		if( $this->get_requests() ) {

			foreach( $this->get_requests() as $request ) {

				if( strtolower( $request->state ) == 'INVALID' && ! empty( $request->errors ) ) {
					foreach( $request->errors as $error ) {
						$errors[] = sprintf( 'Error: %s. Error message: %s. Error type: %s:%s.', WD_Edostavka_Shipping_API_Errors::get_error_text( $error->code ), $error->message, $request->type, $request->state );
					}
				}
			}
		}

		return $errors;
	}

	public function get_token() {
		return array(
			'access_token'	=> $this->access_token,
			'expires_in'	=> $this->expires_in
		);
	}

	public function get_requests() {
		return $this->requests;
	}

	public function get_tariff() {
		return array(
			'delivery_sum'	=> $this->delivery_sum,
			'period_min'	=> $this->period_min,
			'period_max'	=> $this->period_max,
			'weight_calc'	=> $this->weight_calc,
			'total_sum'		=> $this->total_sum,
			'currency'		=> $this->currency,
			'services'		=> $this->services
		);
	}

	/**
	 * @return array Array of postal codes
	 */
	public function get_postcodes() {
		$data = $this->get_response_data();

		return $data->postal_codes ?: array();
	}

	public function get_response_data() {
		return $this->response_data;
	}

	/**
	 * @return string|void
	 * @throws Woodev_API_Exception
	 */
	public function get_webhook_id() {
		return $this->get_entity_uuid();
	}

	/**
	 * @return mixed
	 * @throws Woodev_API_Exception
	 */
	public function get_entity_uuid() {
		$data = $this->get_response_data();

		if( $data && isset( $data->entity, $data->entity->uuid ) ) {
			return $data->entity->uuid;
		} else {
			throw new Woodev_API_Exception( 'Something going wrong. Can not get entity ID.' );
		}
	}

	/**
	 * @return array
	 */
	public function get_related_entities() {

		$data = $this->get_response_data();

		if( empty( $data->related_entities ) ) {
			return ( array ) $data->related_entities;
		} else {
			return array();
		}
	}

	public function get_history_statuses() {
		$data = $this->get_response_data();
		$statuses = array();

		if( ! empty( $data->entity->statuses ) ) {
			$statuses = array_merge( $statuses, ( array ) $data->entity->statuses );
		}

		return $statuses;
	}
}
