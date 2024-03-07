<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Dadata_API extends Woodev_Cacheable_API_Base {

	private $token;

	private $secret;

	public function __construct( $token, $secret = '' ) {
		$this->token    = $token;
		$this->secret   = $secret;

		$this->set_request_content_type_header( 'application/json' );
		$this->set_request_accept_header( 'application/json' );
		$this->set_response_handler( 'WD_Edostavka_Dadata_API_Response' );

		$this->set_request_header( 'Authorization', sprintf( 'Token %s', $this->token ) );

		if( ! empty( $this->secret ) ) {
			$this->set_request_header( 'X-Secret', $this->secret );
		}
	}

	public function get_balance( $refresh = false ) {
		$request = $this->get_new_request( 'core' );
		$request->set_cache_lifetime( 12 * HOUR_IN_SECONDS );
		if( $refresh ) {
			$request->bypass_cache();
		}
		$request->get_balance();

		return $this->perform_request( $request );
	}

	public function get_stats() {
		$request = $this->get_new_request( 'core' );
		$request->set_should_cache( false );
		$request->get_stats();

		return $this->perform_request( $request );
	}

	public function get_ip_local_address( $params = array() ) {
		$request = $this->get_new_request( 'suggestions' );
		$request->set_cache_lifetime( WEEK_IN_SECONDS );
		$request->get_ip_local_address( $params );

		return $this->perform_request( $request );
	}

	public function get_delivery_id( $kladr_id = '' ) {
		$request = $this->get_new_request( 'suggestions' );
		$request->set_cache_lifetime( WEEK_IN_SECONDS );
		$request->get_delivery_id( $kladr_id );

		return $this->perform_request( $request );
	}

	/**
	 * @throws Woodev_API_Exception
	 */
	protected function get_new_request( $request_type = '' ) {
		switch ( $request_type ) {
			//Request to main API
			case 'core' :
				$this->request_uri = 'https://dadata.ru/api/v2';
				break;

			//Request to standardization API
			case 'cleaner' :
				$this->request_uri = 'https://cleaner.dadata.ru/api/v1/clean';
				break;

			//Request to suggestions API
			case 'suggestions' :
				$this->request_uri = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs';
				break;

			default:
				throw new Woodev_API_Exception( __( 'Invalid request type', 'woocommerce-edostavka' ) );
		}

		return new WD_Edostavka_Dadata_API_Request();
	}

	/**
	 * @throws Woodev_API_Exception
	 */
	protected function do_post_parse_response_validation() {
		$response           = $this->get_response();
		$errors             = $response->get_errors();
		$response_code      = $this->get_response_code();
		$response_message   = $this->get_response_message();
		$messages           = array();

		if( 200 !== $response_code ) {
			if( 401 == $response_code || Woodev_Helper::str_starts_with( strtolower( $response_message ), 'unauthorized' ) ) {
				$messages[] = 'Неверно указаны данные авторизации от сервиса DADATA. Пожалуйста, убедитесь что вы указали корректные данные.';
			} else {
				$messages[] = sprintf( 'Во время запроса к API Dadata произошла ошибка. Код ощибки: %s. Текст ошибки: %s.', $response_code, $this->get_response_message() );
			}
		}

		if ( $messages ) {
			throw new Woodev_API_Exception( Woodev_Helper::list_array_items( $messages, 'и' ) );
		}
	}

	protected function get_request_user_agent() {
		return Woodev_Helper::str_convert( parent::get_request_user_agent() );
	}

	protected function get_plugin() {
		return wc_edostavka_shipping();
	}
}
