<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Download_Photo_Webhook extends WC_Edostavka_Webhook {

	public function __construct() {

		parent::__construct( 'download_photo' );
	}

	protected function process_request() {}
}
