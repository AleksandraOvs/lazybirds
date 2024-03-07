<?php

interface WC_Edostavka_Order_Package_Interface {

	/**
	 * @return string|integer
	 */
	public function get_unique_key();
	/**
	 * @return string
	 */
	public function get_number();

	/**
	 * @return integer
	 */
	public function get_weight();

	/**
	 * @return integer
	 */
	public function get_length();

	/**
	 * @return integer
	 */
	public function get_width();

	/**
	 * @return integer
	 */
	public function get_height();

	/**
	 * @return WC_Edostavka_Order_Item_Interface[] Array of Items
	 */
	public function get_items();
}
