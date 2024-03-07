<?php

class WC_Edostavka_Customer_Session_Data_Store extends WC_Edostavka_Customer_Data_Store_Abstract {

	/**
	 * @param WC_Edostavka_Customer_Location_Data $customer
	 *
	 * @return void
	 */
	public function read( &$customer ) {
		$customer->set_defaults();

		/*if( wp_using_ext_object_cache() ) {
			$props = ( array ) wp_cache_get( $customer->get_object_type(), $this->get_cache_name() );
		} else {
			$props = ( array ) get_transient( $this->get_cache_name() );
		}*/

		$props = WC()->session->get( $customer->get_object_type(), array() );

		$customer->set_location( $props );
		$customer->set_object_read( true );
	}

	/**
	 * @param WC_Edostavka_Customer_Location_Data $customer
	 *
	 * @return void
	 */
	public function create( &$customer ) {
		$data = $this->has_changes( $customer );
		if( $data ) {
			/*if( wp_using_ext_object_cache() ) {
				wp_cache_set( $customer->get_object_type(), $data, $this->get_cache_name(), DAY_IN_SECONDS * 2 );
			} else {
				set_transient( $this->get_cache_name(), $data, DAY_IN_SECONDS * 2 );
			}*/
			WC()->session->set( $customer->get_object_type(), $data );
		}
	}

	public function update( &$customer ) {}

	public function delete( &$customer, $args = array() ) {
		/*if( wp_using_ext_object_cache() ) {
			wp_cache_delete( $customer->get_object_type(), $this->get_cache_name() );
		} else {
			delete_transient( $this->get_cache_name() );
		}*/
		WC()->session->set( $customer->get_object_type(), null );
	}

	private function get_cache_name() {
		$customer_id = WC()->session->get_customer_id();
		$group_name = sprintf( 'customer_location_%s', $customer_id );

		if( wp_using_ext_object_cache() ) {
			return WC_Cache_Helper::get_cache_prefix( $group_name );
		} else {
			return WC_Cache_Helper::get_transient_version( $group_name );
		}
	}
}
