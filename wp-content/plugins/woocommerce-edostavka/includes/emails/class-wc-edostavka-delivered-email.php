<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'WC_CDEK_Delivered_Email', false ) ) :

class WC_CDEK_Delivered_Email extends WC_Edostavka_Email {

	public function __construct() {

		$this->id               = sprintf( '%s_delivered_order', wc_edostavka_shipping()->get_method_id() );
		$this->title            = __( 'CDEK - order is delivered', 'woocommerce-edostavka' );
		$this->description      = __( 'Notification of the administrator about the successful delivery of the order to the customer.', 'woocommerce-edostavka' );
		$this->has_preview      = true;
		$this->customer_email   = false;
		$this->template_html    = 'emails/delivered-email.php';
		$this->template_plain   = 'emails/plain/delivered-email.php';

		parent::__construct();

		add_action( 'wc_edostavka_order_delivered_notification', array( $this, 'trigger' ), 10, 2 );
	}

	public function init_form_fields() {
		parent::init_form_fields();

		if( isset( $this->form_fields['additional_content'] ) ) {
			unset( $this->form_fields['additional_content'] );
		}
	}

	/**
	 * В данном методе нет никакой необходимости.
	 * Просто что-бы помнить, что так вот можно менять параметры передаваемые в шаблон письма.
	 *
	 * @param string $type
	 *
	 * @return array
	 */
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
		return __( '[{site_title}]: Order #{order_number} is delivered', 'woocommerce-edostavka' );
	}

	public function get_default_heading() {
		return __( 'Congratulation! Order #{order_number} was successfully delivered.', 'woocommerce-edostavka' );
	}

	public function get_default_message_text() {
		return __( 'Order #{order_number} on your website <a href="{site_url}" target="_blank">{site_title}</a> was successfully delivered. Now, you can touch with the client and take details.', 'woocommerce-edostavka' );
	}
}

endif;
