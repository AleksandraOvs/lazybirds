<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Print_Form_Webhook extends WC_Edostavka_Webhook {

	public function __construct() {

		parent::__construct( 'print_form' );
	}

	protected function process_request() {

	}
}
