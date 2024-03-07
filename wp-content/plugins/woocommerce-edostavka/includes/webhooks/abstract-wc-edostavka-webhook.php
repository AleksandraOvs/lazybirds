<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The base webhook class.
 *
 * @since 2.2.0
 */
abstract class WC_Edostavka_Webhook {

	/** @var string webhook resource **/
	protected $resource;

	/** @var string raw request data **/
	protected $raw_request_data;

	/** @var string request data, decoded **/
	protected $request_data;

	/**
	 * Construct the class.
	 *
	 * @since 2.2.0
	 */
	public function __construct( $resource ) {

		$this->resource = $resource;

		add_action( 'woocommerce_api_wc_edostavka_' . $this->resource, array( $this, 'handle_request' ) );
	}

	/**
	 * Determine if this webhook is enabled.
	 *
	 * @since 2.2.0
	 * @return bool
	 */
	protected function enabled() {
		return true;
	}

	/**
	 * Handle the request.
	 *
	 * @since 2.2.0
	 */
	public function handle_request() {

		try {

			if ( ! $this->enabled() ) {
				throw new Woodev_Plugin_Exception( sprintf( __( 'The %s webhook is disabled.', 'woocommerce-edostavka' ), $this->resource ) );
			}

			// log the request data
			$this->log_request();

			$this->validate_request();

			$this->process_request();

		} catch ( Woodev_Plugin_Exception $e ) {

			wc_edostavka_shipping()->log( sprintf( __( '[Webhook Error]: %s', 'woocommerce-edostavka' ), $e->getMessage() ), 'edostavka_webhooks' );
		}

		status_header( $this->enabled() ? 200 : 405 );
		die();
	}

	/**
	 * Validate the webhook request.
	 *
	 * @return void
	 * @throws Woodev_Plugin_Exception
	 * @since 2.2.0
	 */
	protected function validate_request() {

		if ( ! $this->get_request_body() ) {
			throw new Woodev_Plugin_Exception( __( 'Invalid data.', 'woocommerce-edostavka' ) );
		}

		// If not a request of this type, bail
		if ( $this->resource !== strtolower( $this->get_request_type() ) ) {
			throw new Woodev_Plugin_Exception( __( 'Invalid resource.', 'woocommerce-edostavka' ) );
		}

		$existing_webhooks = get_option( 'wc_edostavka_webhook_ids', array() );

		if( $existing_webhooks[ $this->resource ] !== $this->get_request_uuid() ) {
			throw new Woodev_Plugin_Exception( __( 'Invalid webhook ID.', 'woocommerce-edostavka' ) );
		}
	}

	/**
	 * Process the request after validation.
	 *
	 * @since 2.2.0
	 */
	protected function process_request() {}

	/**
	 * Get the raw request body.
	 *
	 * @since 2.2.0
	 * @return string
	 */
	protected function get_raw_request_data() {

		if ( is_null( $this->raw_request_data ) ) {
			$this->raw_request_data = file_get_contents( 'php://input' );
		}

		return $this->raw_request_data;
	}

	/**
	 * Get the request data, decoded.
	 *
	 * @since 2.2.0
	 * @return string
	 */
	protected function get_request_data() {

		return json_decode( $this->get_raw_request_data() );
	}

	/**
	 * Get the request type.
	 *
	 * @since 2.2.0
	 * @return string
	 */
	protected function get_request_type() {

		$data = $this->get_request_data();

		return isset( $data->type ) ? $data->type : '';
	}

	protected function get_request_uuid() {

		$data = $this->get_request_data();

		return isset( $data->uuid ) ? $data->uuid : '';
	}

	/**
	 * Get the request body.
	 *
	 * @since 2.2.0
	 * @return string
	 */
	protected function get_request_body() {

		return $this->get_request_data();
	}

	protected function get_request_attributes() {
		$data = $this->get_request_data();
		return isset( $data->attributes ) ? $data->attributes : '';
	}

	/**
	 * Log the webhook request.
	 *
	 * @since 2.2.0
	 */
	protected function log_request() {
		wc_edostavka_shipping()->log( sprintf( __( 'Webhook Request Body: %s', 'woocommerce-edostavka' ), $this->get_raw_request_data() ), 'edostavka_webhooks' );
	}
}
