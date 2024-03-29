<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! interface_exists( 'Woodev_Payment_Gateway_API_Request' ) ) :

	/**
	 * WooCommerce Direct Payment Gateway API Request
	 */
	interface Woodev_Payment_Gateway_API_Request extends Woodev_API_Request {}

endif;