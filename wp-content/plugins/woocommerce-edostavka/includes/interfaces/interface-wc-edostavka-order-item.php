<?php

interface WC_Edostavka_Order_Item_Interface {

	/**
	 * Get Item name
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Get Item SKU or ID
	 *
	 * @return string
	 */
	public function get_ware_key();

	/**
	 * Get Item cost
	 *
	 * @return float
	 */
	public function get_value();

	/**
	 * @return float
	 */
	public function get_cost();

	/**
	 * @return integer
	 */
	public function get_weight();

	/**
	 * @return integer
	 */
	public function get_weight_gross();

	/**
	 * @return integer
	 */
	public function get_amount();
}
