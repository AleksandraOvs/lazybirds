<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Webhook_Handler {

	/** @var array the registered webhooks **/
	protected $webhooks;

	/**
	 * Construct the handler.
	 */
	public function __construct() {

		require_once( wc_edostavka_shipping()->get_plugin_path() . '/includes/webhooks/abstract-wc-edostavka-webhook.php' );
		require_once( wc_edostavka_shipping()->get_plugin_path() . '/includes/webhooks/class-wc-edostavka-order-webhook.php' );
		require_once( wc_edostavka_shipping()->get_plugin_path() . '/includes/webhooks/class-wc-edostavka-print-form-webhook.php' );
		require_once( wc_edostavka_shipping()->get_plugin_path() . '/includes/webhooks/class-wc-edostavka-download-photo-webhook.php' );

		$classes = array(
			'WC_Edostavka_Order_Webhook',
			'WC_Edostavka_Print_Form_Webhook',
			'WC_Edostavka_Download_Photo_Webhook'
		);

		foreach ( $classes as $class ) {
			$this->webhooks[] = new $class;
		}
	}

	/**
	 * Generate and save the webhooks data.
	 */
	public function set_webhooks() {
		$this->create_remote_webhooks();
	}

	/**
	 * Remove the webhooks data.
	 */
	public function remove_webhooks() {
		$this->delete_remote_webhooks();
	}


	/**
	 * Reset the webhook data.
	 */
	public function reset_webhooks() {

		$this->remove_webhooks();
		$this->set_webhooks();
	}

	/**
	 * Create new webhooks via the CDEK API.
	 */
	public function create_remote_webhooks() {

		$new_webhooks = array();

		foreach ( array( 'order_status', 'print_form', 'download_photo' ) as $resource ) {

			try {

				$webhook = wc_edostavka_shipping()->get_api()->create_webhook( strtoupper( $resource ), WC()->api_request_url( sprintf( 'wc_edostavka_%s', $resource ) ) );

				if( $webhook ) {
					$new_webhooks[ $resource ] = $webhook;
				}

			} catch ( Woodev_API_Exception $e ) {

				wc_edostavka_shipping()->log( $e->getMessage() );
			}
		}

		update_option( 'wc_edostavka_webhook_ids', $new_webhooks );
	}


	/**
	 * Delete existing webhooks from the CDEK API.
	 */
	public function delete_remote_webhooks() {

		$existing_webhooks = get_option( 'wc_edostavka_webhook_ids', array() );

		foreach ( $existing_webhooks as $type => $id ) {

			try {

				wc_edostavka_shipping()->get_api()->delete_webhook( $id );

				unset( $existing_webhooks[ $type ] );

			} catch ( Woodev_API_Exception $e ) {

				wc_edostavka_shipping()->log( $e->getMessage() );
			}
		}

		update_option( 'wc_edostavka_webhook_ids', $existing_webhooks );
	}
}
