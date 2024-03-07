<?php

defined( 'ABSPATH' ) || exit;

abstract class WC_Edostavka_Customer_Data_Store_Abstract extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

	/**
	 * @param $customer
	 */
	abstract public function create( &$customer );

	/**
	 * @param $customer
	 */
	abstract public function read( &$customer );

	/**
	 * @param $customer
	 */
	abstract public function update( &$customer );

	/**
	 * @param WC_Edostavka_Customer_Location_Data $customer
	 * @param array $args
	 *
	 * @return void
	 */
	abstract public function delete( &$customer, $args = array() );

	/**
	 * @param $customer
	 *
	 * @return array|null
	 */
	public function has_changes( $customer ) {

		$changes = $customer->get_changes();

		if( $changes && array_intersect_key( $customer->get_data_keys(), array_keys( $changes ) ) ) {
			return $customer->get_location( 'edit' );
		}

		return null;
	}
}
