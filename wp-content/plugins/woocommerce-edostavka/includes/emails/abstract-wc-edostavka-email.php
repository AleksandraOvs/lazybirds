<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Edostavka_Email', false ) ) {
	return;
}

abstract class WC_Edostavka_Email extends WC_Email {

	protected $has_preview = false;

	/**
	 * Constructor.
	 */
	public function __construct() {

		if ( is_null( $this->template_base ) ) {
			$this->template_base = trailingslashit( wc_edostavka_shipping()->get_template_path() );
		}

		if ( ! $this->customer_email ) {
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		$this->set_placeholder( 'order_number', '' );
		$this->set_placeholder( 'data', '' );
		$this->set_placeholder( 'order_date', '' );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Trigger the sending of this email.
	 */
	public function trigger( $order_id, $order = false ) {

		if ( method_exists( $this, 'setup_locale' ) ) {
			$this->setup_locale();
		}

		if ( $order_id && ! is_a( $order, 'WC_Edostavka_Shipping_Order' ) ) {
			$order = new WC_Edostavka_Shipping_Order( $order_id );
		}

		$this->object = $order;

		if ( $this->customer_email && ! $this->recipient ) {
			$this->recipient = $this->object->get_billing_email();
		}

		$this->set_placeholder( 'order_number', $this->object->get_order_number() );
		$this->set_placeholder( 'data', wc_format_datetime( $this->object->get_date_created() ) );
		$this->set_placeholder( 'order_date', wc_format_datetime( $this->object->get_date_created() ) );

		if ( $this->is_enabled() && $this->get_recipient() ) {
			if( $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ) ) {
				do_action( 'wc_edostavka_after_send_email_' . $this->id, $order_id, $this );
			}
		}

		if ( method_exists( $this, 'restore_locale' ) ) {
			$this->restore_locale();
		}
	}

	public function set_placeholder( $key, $value ) {
		$this->placeholders[ "{{$key}}" ] = $value;
	}

	/**
	 * Gets the content arguments.
	 *
	 * @param string $type Optional. The content type [html, plain].
	 *
	 * @return array
	 */
	public function get_content_args( $type = 'html' ) {
		return array(
			'order'                 => $this->object,
			'email_heading'         => $this->get_heading(),
			'additional_content'    => $this->get_additional_content(),
			'message_text' 		    => $this->format_string( $this->get_option( 'message_text', $this->get_default_message_text() ) ),
			'sent_to_admin'         => ! $this->customer_email,
			'plain_text'            => ( 'plain' === $type ),
			'email'                 => $this
		);
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			$this->get_content_args(),
			'',
			$this->template_base
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			$this->get_content_args( 'plain' ),
			'',
			$this->template_base
		);
	}

	public function init_form_fields() {
		parent::init_form_fields();

		$placeholder_text   = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );

		$fields = Woodev_Helper::array_insert_after( $this->form_fields, 'heading', array(
			'message_text'  => array(
				'title'       	=> __( 'Message text', 'woocommerce-edostavka' ),
				'type'        	=> 'textarea',
				'css'         	=> 'width:400px; height: 100px;',
				'desc_tip' 		=> sprintf( __( 'Enter message that will be showing on email body. %s'), $placeholder_text ),
				'placeholder' 	=> $this->get_default_message_text(),
				'default'     	=> $this->get_default_message_text(),
			)
		) );

		$this->form_fields = $fields;
	}

	public function get_default_message_text() {
		return '';
	}

	public function has_preview() {
		return ( bool ) $this->has_preview;
	}

	public function preview_emails() {
		if( $this->has_preview() && $order = $this->get_preview_order() ) {

			$this->object = $order;

			$this->set_placeholder( 'order_number', $order->get_order_number() );
			$this->set_placeholder( 'data', wc_format_datetime( $order->get_date_created() ) );
			$this->set_placeholder( 'order_date', wc_format_datetime( $order->get_date_created() ) );

			echo $this->style_inline( $this->get_content_html() );
		}
	}

	/**
	 * @return false|WC_Edostavka_Shipping_Order
	 */
	protected function get_preview_order() {

		$order = false;

		$order_query = new WP_Query( array(
			'post_type'         => wc_get_order_types( 'view-orders' ),
			'post_status'       => array_keys( wc_get_order_statuses() ),
			'nopaging'          => true,
			'fields'            => 'ids',
			'meta_query'        => array(
				array(
					'key'       => '_wc_edostavka_shipping',
					'compare'   => 'EXISTS'
				),
				array(
					'key'       => '_wc_edostavka_cdek_order_id',
					'compare'   => 'EXISTS'
				),
				array(
					'key'       => '_wc_edostavka_status',
					'value'     => array( 'NEW', 'CANCELED' ),
					'compare'   => 'NOT IN'
				),
			)
		) );

		if( absint( $order_query->found_posts ) > 0 ) {
			$orders = $order_query->get_posts();
			$order = new WC_Edostavka_Shipping_Order( $orders[0] );
		}

		return $order;
	}

}
