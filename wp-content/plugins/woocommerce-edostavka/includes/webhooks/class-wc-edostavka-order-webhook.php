<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The order webhook class.
 */
class WC_Edostavka_Order_Webhook extends WC_Edostavka_Webhook {

	/**
	 * Construct the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct( 'order_status' );
	}

	/**
	 * Determine if this webhook is enabled.
	 *
	 * @return bool
	 */
	protected function enabled() {
		return wc_string_to_bool( wc_edostavka_shipping()->get_integration_handler()->get_option( 'auto_update_orders', 'yes' ) );
	}

	/**
	 * Process the request after validation.
	 * @throws Woodev_Plugin_Exception
	 */
	protected function process_request() {

		$attributes = $this->get_request_attributes();

		if( empty( $attributes ) ) {
			throw new Woodev_Plugin_Exception( __( 'Invalid request attributes', 'woocommerce-edostavka' ) );
		}

		$order_number = $attributes->number;
		$order_prefix = wc_edostavka_shipping()->get_integration_handler()->get_option( 'order_prefix' );

		if( ! empty( $order_prefix ) ) {
			$order_number = str_replace( $order_prefix, '', $order_number );
		}

		$order = wc_get_order( $order_number );

		if ( ! $order ) {
			throw new Woodev_Plugin_Exception( sprintf( __( 'Order %s not found.', 'woocommerce-edostavka' ), $order_number ) );
		}

		$order = new WC_Edostavka_Shipping_Order( $order );
		$order->update_order();

	}

	private function get_status_key( $status_code ) {
		$statuses = array(
			'1' => 'CREATED',
			'2' => 'CANCELED',
			'4' => 'DELIVERED',
			'5' => 'NOT_DELIVERED'
		);

		return isset( $statuses[ $status_code ] ) ? $statuses[ $status_code ] : null;
	}
}
