<?php

/**
 * WC Edostavka Order Interface
 *
 * @version  2.2.0
 */
interface WC_Edostavka_Order_Interface {

	public function get_order_number();

	public function get_tariff_code();

	public function get_items();

	public function get_packages();

	public function get_status();

	public function get_seller();

	public function get_sender();

	public function get_delivery_cost();
}
