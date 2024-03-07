<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'WC_CDEK_Not_Delivered_Email', false ) ) :

class WC_CDEK_Not_Delivered_Email extends WC_Edostavka_Email {

	public function __construct() {

		$this->id               = sprintf( '%s_not_delivered_order', wc_edostavka_shipping()->get_method_id() );
		$this->title            = __( 'CDEK - order is not delivered', 'woocommerce-edostavka' );
		$this->description      = __( 'Notification of the administrator about the failed delivery of the order to the customer.', 'woocommerce-edostavka' );
		$this->has_preview      = true;
		$this->customer_email   = false;
		$this->template_html    = 'emails/not-delivered-email.php';
		$this->template_plain   = 'emails/plain/not-delivered-email.php';

		parent::__construct();

		add_action( 'wc_edostavka_order_not_delivered_notification', array( $this, 'trigger' ), 10, 2 );
	}

	public function init_form_fields() {
		parent::init_form_fields();

		if( isset( $this->form_fields['additional_content'] ) ) {
			unset( $this->form_fields['additional_content'] );
		}
	}

	public function get_content_args( $type = 'html' ) {
		$args = parent::get_content_args( $type );

		$args['content'] = $args['message_text'];
		unset( $args['message_text'] );

		if( isset( $args['additional_content'] ) ) {
			unset( $args['additional_content'] );
		}

		return $args;
	}

	public function get_default_subject() {
		return __( '[{site_title}]: Order #{order_number} is not delivered', 'woocommerce-edostavka' );
	}

	public function get_default_heading() {
		return __( 'Unfortunately, order #{order_number} was not delivered.', 'woocommerce-edostavka' );
	}

	public function get_default_message_text() {
		return __( 'Order #{order_number} was not delivered to the customer.', 'woocommerce-edostavka' );
	}
}

endif;
