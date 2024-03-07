<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Customer_Data_Store extends WC_Edostavka_Customer_Data_Store_Abstract {

	/**
	 * Internal meta type used to store user data.
	 *
	 * @var string
	 */
	protected $meta_type = 'user';

	/**
	 * @param WC_Edostavka_Customer_Location_Data $customer
	 *
	 * @return void
	 */
	public function read( &$customer ) {

		$customer->set_defaults();
		//$props = ( array ) get_user_meta( $customer->get_id(), $customer->get_object_type(), true );
		$props = ( array ) $customer->get_meta( $customer->get_object_type(), true, 'edit' );

		$customer->set_location( $props );
		$customer->read_meta_data();
		$customer->set_object_read( true );
	}

	public function create( &$customer ) {}

	/**
	 * @param WC_Edostavka_Customer_Location_Data $customer
	 *
	 * @return void
	 */
	public function update( &$customer ) {

		$data = $this->has_changes( $customer );

		if( $data ) {
			//update_user_meta( $customer->get_id(), $customer->get_object_type(), $data );
			$customer->update_meta_data( $customer->get_object_type(), $data );
			$customer->save_meta_data();
			$customer->apply_changes();
		}

	}

	public function delete( &$customer, $args = array() ) {
		$customer->delete_meta_data( $customer->get_object_type() );
	}
}
