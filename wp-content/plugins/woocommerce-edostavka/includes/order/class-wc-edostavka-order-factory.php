<?php
/**
 * Order Factory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Edostavka Order factory class
 */
class WC_Edostavka_Order_Factory implements WC_Edostavka_Order_Interface {

	/**
	 * @var WC_Order $order
	 */
	private $order;

	/** @var string $packing_method */
	private $packing_method;

	public function __construct( WC_Order $order ) {
		$this->order = $order;
	}

	public function get_order() {
		return $this->order;
	}

	public function get_order_number() {
		return $this->order->get_order_number();
	}

	/**
	 * @return WC_Order_Item|WC_Order_Item_Shipping|null
	 */
	public function get_shipping_item() {
		$shipping_methods = $this->order->get_shipping_methods();
		return array_shift( $shipping_methods );
	}

	/**
	 * @return bool|WC_Shipping_Method
	 */
	public function get_shipping_method() {
		return WC_Shipping_Zones::get_shipping_method( $this->get_shipping_item()->get_instance_id() );
	}

	/**
	 * @return string
	 */
	public function get_tariff_code() {
		return $this->get_shipping_method()->get_option( 'tariff' );
	}

	/**
	 * @return string String door or stock
	 */
	public function get_tariff_from() {
		$tariff_data = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $this->get_tariff_code() );
		$tariff_type = $tariff_data['type'];
		list( $from ) = explode( '_', $tariff_type );
		return $from;
	}

	/**
	 * @return string String door, stock or postamat
	 */
	public function get_tariff_to() {
		$tariff_data = WC_Edostavka_Tariffs_Data::get_tariff_by_code( $this->get_tariff_code() );
		$tariff_type = $tariff_data['type'];
		list( , $to ) = explode( '_', $tariff_type );
		return $to;
	}

	public function get_items() {
		return $this->order->get_items();
	}

	public function get_packages() {
		$order_packages = new WC_Edostavka_Order_Packages( $this );
		return $order_packages->get_packages();
	}

	public function get_status() {
		//return $this->order->get_order_status();
	}

	/**
	 * @return array
	 */
	public function get_seller() {
		return array(
			'name'              => wc_edostavka_shipping()->get_integration_handler()->get_option( 'seller_name' ),
			'inn'               => wc_edostavka_shipping()->get_integration_handler()->get_option( 'seller_inn' ),
			'phone'             => wc_sanitize_phone_number( wc_edostavka_shipping()->get_integration_handler()->get_option( 'seller_phone' ) ),
			'ownership_form'    => wc_edostavka_shipping()->get_integration_handler()->get_option( 'seller_ownership_form' ),
			'address'           => wc_edostavka_shipping()->get_integration_handler()->get_option( 'seller_address' )
		);
	}

	/**
	 * @return array
	 */
	public function get_sender() {
		return array(
			'company'   => wc_edostavka_shipping()->get_integration_handler()->get_option( 'sender_company' ),
			'name'      => wc_edostavka_shipping()->get_integration_handler()->get_option( 'sender_name' ),
			'email'     => wc_edostavka_shipping()->get_integration_handler()->get_option( 'sender_email' ),
			'phones'    => array(
				'number'    => wc_edostavka_shipping()->get_integration_handler()->get_option( 'sender_phone' )
			)
		);
	}

	/**
	 * @return array
	 */
	public function get_recipient() {
		return array(
			'company'   => $this->order->get_billing_company(),
			'name'      => $this->order->get_formatted_billing_full_name(),
			'email'     => $this->order->get_billing_email(),
			'phones'    => array(
				'number'    => $this->order->get_billing_phone()
			)
		);
	}

	/**
	 * Get order delivery cost
	 *
	 * @return string|int
	 */
	public function get_delivery_cost() {

		if( 'cod' === $this->order->get_payment_method() && $this->order->get_shipping_total() > 0 ) {
			return $this->order->get_shipping_total();
		}

		return 0;
	}

	public function get_packing_method() {
		return $this->packing_method;
	}

	public function set_packing_method( $packing_method = null ) {
		$this->packing_method = $packing_method;
	}
}
