<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'WC_CDEK_Tracking_Email', false ) ) :

class WC_CDEK_Tracking_Email extends WC_Edostavka_Email {

	public function __construct() {

		$this->id          		= sprintf( '%s_tracking', wc_edostavka_shipping()->get_method_id() );
		$this->customer_email   = true;
		$this->has_preview      = true;
		$this->title       		= __( 'CDEK - send tracking number', 'woocommerce-edostavka' );
		$this->description 		= __( 'Tracking number emails sent to customers when order has been exported to CDEK successfully.', 'woocommerce-edostavka' );
		$this->template_html    = 'emails/tracking-code.php';
		$this->template_plain   = 'emails/plain/tracking-code.php';

		$this->set_placeholder( 'tracking_code_url', '' );
		$this->set_placeholder( 'tracking_code', '' );

		parent::__construct();

		add_action( 'wc_edostavka_after_send_email_' . $this->id, array( $this, 'mark_order_as_send_email' ) );
	}

	public function trigger( $order_id, $order = false, $tracking_number = null ) {

		if ( $order_id && ! is_a( $order, 'WC_Edostavka_Shipping_Order' ) ) {
			$order = new WC_Edostavka_Shipping_Order( $order_id );
		}

		$tracking_number = is_null( $tracking_number ) ? wc_edostavka_get_tracking_code( $order ) : $tracking_number;

		$this->set_placeholder( 'tracking_code_url', $this->get_tracking_code_url( $tracking_number, $order ) );
		$this->set_placeholder( 'tracking_code', $tracking_number );

		parent::trigger( $order_id, $order );
	}

	public function mark_order_as_send_email( $order_id ) {
		if( is_numeric( $order_id ) && $order_id > 0 ) {
			$order = new WC_Edostavka_Shipping_Order( $order_id );
			$order->update_order_meta( 'tracking_number_email_send', true );
		}
	}

	public function get_tracking_code_url( $tracking_number, $order = false ) {

		$view_order_url = false;

		if( $order && is_a( $order, 'WC_Edostavka_Shipping_Order') ) {
			$view_order_url = $order->get_view_order_url();
		} elseif( $this->object instanceof WC_Order ) {
			$view_order_url = $this->object->get_view_order_url();
		}

		if( $view_order_url ) {
			$url = sprintf( '<a href="%s#wc-edostavka-tracking">%s</a>', $view_order_url, $tracking_number );
			return apply_filters( 'woocommerce_edostavka_email_tracking_core_url', $url, $tracking_number, $this->object );
		}

	}

	public function get_default_subject() {
		return __( '[{site_title}] - Order #{order_number} was exported to CDEK.', 'woocommerce-edostavka' );
	}

	public function get_default_heading() {
		return __( 'Your order #{order_number} has been assigned a tracking number.', 'woocommerce-edostavka' );
	}

	public function get_default_message_text() {
		return __( 'Your order #{order_number} on web-site <a href="{site_url}" target="_blank">{site_title}</a> has been exported to <a href="https://cdek.ru/" target="_blank">CDEK delivery service</a> with tracking number {tracking_code}. You can track your order status <a href="https://www.cdek.ru/ru/tracking?order_id={tracking_code}">on the CDEK website</a>.', 'woocommerce-edostavka' );
	}

	public function get_default_additional_content() {
		return __( 'Thanks for shopping with us. If you have any problems with delivery, please let us know.', 'woocommerce-edostavka' );
	}

	public function preview_emails() {

		if( $order = $this->get_preview_order() ) {

			$this->object = $order;

			$tracking_number = wc_edostavka_get_tracking_code( $order );

			$this->set_placeholder( 'tracking_code_url', $this->get_tracking_code_url( $tracking_number, $order ) );
			$this->set_placeholder( 'tracking_code', $tracking_number );
		}

		parent::preview_emails();
	}
}

endif;
