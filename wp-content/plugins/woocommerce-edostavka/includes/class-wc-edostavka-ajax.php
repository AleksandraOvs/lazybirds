<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_AJAX {

	public function __construct() {

		add_action( 'wp_ajax_edostavka_get_tariff_by_code', array( $this, 'get_tariff_by_code' ) );

		add_action( 'wp_ajax_nopriv_edostavka_get_location_cities', array( $this, 'get_location_cities' ) );
		add_action( 'wp_ajax_edostavka_get_location_cities', array( $this, 'get_location_cities' ) );

		add_action( 'wp_ajax_nopriv_edostavka_get_deliverypoints', array( $this, 'get_delivery_points' ) );
		add_action( 'wp_ajax_edostavka_get_deliverypoints', array( $this, 'get_delivery_points' ) );

		add_action( 'wp_ajax_nopriv_edostavka_set_customer_location', array( $this, 'set_customer_location' ) );
		add_action( 'wp_ajax_edostavka_set_customer_location', array( $this, 'set_customer_location' ) );
		/**
		 * If request doing via WC_AJAX method
		 */
		add_action( 'wc_ajax_edostavka_set_customer_location', array( $this, 'set_customer_location' ), 5 );
		add_action( 'wc_ajax_edostavka_set_customer_location_dadata', array(
			$this,
			'set_customer_location_by_dadata'
		), 5 );

		add_action( 'wp_ajax_nopriv_edostavka_set_customer_location_by_id', array(
			$this,
			'set_customer_location_by_id'
		) );
		add_action( 'wp_ajax_edostavka_set_customer_location_by_id', array( $this, 'set_customer_location_by_id' ) );

		add_action( 'wp_ajax_nopriv_edostavka_set_delivery_point', array( $this, 'set_delivery_point' ) );
		add_action( 'wp_ajax_edostavka_set_delivery_point', array( $this, 'set_delivery_point' ) );

		add_action( 'wp_ajax_edostavka_order_action', array( $this, 'order_actions' ) );
		add_action( 'wp_ajax_edostavka_create_courier_call', array( $this, 'create_courier_call' ) );
		add_action( 'wp_ajax_edostavka_get_order_details', array( $this, 'get_order_details' ) );
	}

	public function get_tariff_by_code() {

		if ( isset( $_POST['code'] ) && ! empty( $_POST['code'] ) ) {
			wp_send_json_success( WC_Edostavka_Tariffs_Data::get_tariff_by_code( wc_clean( $_POST['code'] ) ) );
		}

		wp_send_json_error( 'Во время запроса произошла неизвестная ошибка.' );
	}

	public function get_location_cities() {

		if ( ! empty( $_POST ) ) {

			$data = $_POST;

			if ( isset( $data['action'] ) ) {
				unset( $data['action'] );
			}

			if ( isset( $data['beforeSend'] ) ) {
				unset( $data['beforeSend'] );
			}

			if ( empty( $data['city'] ) && ! empty( $data['country_codes'] ) ) {
				wp_send_json_success( wc_edostavka_get_preloaded_locations( $data['country_codes'] ) );
			} else {
				wp_send_json_success( wc_edostavka_get_location_cities( array_filter( array_map( 'wc_clean', $data ) ) ) );
			}
		}

		wp_send_json_error( 'Во время запроса произошла неизвестная ошибка.' );
	}

	public function get_delivery_points() {

		if ( ! empty( $_REQUEST ) ) {

			$data = $_REQUEST;

			if ( isset( $data['action'] ) ) {
				unset( $data['action'] );
			}

			if ( isset( $data['beforeSend'] ) ) {
				unset( $data['beforeSend'] );
			}

			$response = array(
				'success' => true,
				'data'    => wc_edostavka_get_deliverypoints( array_filter( array_map( 'wc_clean', $data ) ) )
			);

			if ( isset( $data['city_code'], $data['type'] ) ) {
				$chosen_delivery_point = wc_edostavka_get_delivery_point_data( $data['city_code'], strtolower( $data['type'] ) );
				if ( ! empty( $chosen_delivery_point ) ) {
					$response['chosen_delivery_point'] = $chosen_delivery_point;
				}

			}

			wp_send_json( $response );
		}

		wp_send_json_error( 'Во время запроса произошла неизвестная ошибка.' );
	}

	/**
	 * @return void
	 */
	public function set_customer_location() {

		if ( ! empty( $_POST ) && ! empty( $_POST['location'] ) ) {

			if ( isset( $data['action'] ) ) {
				unset( $data['action'] );
			}

			try {

				$location_data = wc_clean( $_POST['location'] );

				if ( ( ! isset( $location_data['isCustom'] ) || ! wc_string_to_bool( wc_edostavka_shipping()->get_integration_handler()->get_option( 'enable_custom_city' ) ) ) && ! $location_data['code'] ) {
					throw new Exception( 'Данные о городе получателя не были установлены так как код города не определён' );
				}

				do_action( 'wc_edostavka_set_customer_location', $location_data );

				/** @var array[] $location_props */
				$location_props = array(
					'country_code' => isset( $location_data['country_code'] ) ? wc_clean( wp_unslash( $location_data['country_code'] ) ) : null,
					'region_code'  => isset( $location_data['region_code'] ) ? intval( $location_data['region_code'] ) : null,
					'region'       => isset( $location_data['region'] ) ? wc_clean( wp_unslash( $location_data['region'] ) ) : null,
					'city_code'    => isset( $location_data['code'] ) ? intval( $location_data['code'] ) : null,
					'city'         => isset( $location_data['id'] ) ? $location_data['id'] : null,
					'longitude'    => isset( $location_data['longitude'] ) ? $location_data['longitude'] : null,
					'latitude'     => isset( $location_data['latitude'] ) ? $location_data['latitude'] : null
				);

				$customer_location = wc_edostavka_shipping()->get_customer_handler();
				$customer_location->set_location( $location_props );
				wp_send_json_success( $customer_location->get_location() );
				//$customer_location->save();

			} catch ( Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}
		}

		wp_send_json_error( 'Во время запроса произошла неизвестная ошибка.' );
	}

	public function set_customer_location_by_dadata() {

		if ( ! empty( $_POST ) && ! empty( $_POST['data'] ) ) {

			$data   = wc_clean( $_POST['data'] );
			$params = array( 'size' => 1 );

			if ( ! empty( $data['fias_id'] ) ) {
				$params['fias_guid'] = $data['fias_id'];
			} else {
				$params['city']        = $data['city'] || $data['settlement'];
				$params['postal_code'] = $data['postal_code'];
			}

			try {

				$location = wc_edostavka_shipping()->get_api()->get_location_cities( $params )->get_response_data();

				if ( ! empty( $location[0] ) ) {

					/** @var array[] $props */
					$props = array(
						'country_code'     => isset( $location[0]->country_code ) ? wc_clean( wp_unslash( $location[0]->country_code ) ) : null,
						'region_code'      => isset( $location[0]->region_code ) ? intval( $location[0]->region_code ) : null,
						'region'           => isset( $location[0]->region ) ? wc_clean( wp_unslash( $location[0]->region ) ) : null,
						'fias_region_guid' => isset( $location[0]->fias_region_guid ) ? $location[0]->fias_region_guid : null,
						'city_code'        => isset( $location[0]->code ) ? intval( $location[0]->code ) : null,
						'city'             => isset( $location[0]->city ) ? $location[0]->city : null,
						'fias_guid'        => isset( $location[0]->fias_guid ) ? $location[0]->fias_guid : null,
						'kladr_code'       => isset( $location[0]->kladr_code ) ? $location[0]->kladr_code : null,
						'longitude'        => isset( $location[0]->longitude ) ? $location[0]->longitude : null,
						'latitude'         => isset( $location[0]->latitude ) ? $location[0]->latitude : null,
						'time_zone'        => isset( $location[0]->time_zone ) ? $location[0]->time_zone : null,
					);

					$customer_location = wc_edostavka_shipping()->get_customer_handler();
					$customer_location->set_location( $props );

					wp_send_json_success( $customer_location->get_location() );

				} else {
					throw new Exception( 'Не удалось получить информацию о населённом пункте.' );
				}

			} catch ( Exception $error ) {
				wp_send_json_error( $error->getMessage() );
			}
		}

		wp_send_json_error( 'Локация пользователя не установлена, так как не были переданы обязательные параметры.' );
	}

	public function set_customer_location_by_id() {

		if ( ! empty( $_POST ) && ! empty( $_POST['code'] ) ) {
			if ( isset( $data['action'] ) ) {
				unset( $data['action'] );
			}

			$code = intval( $_POST['code'] );

			$location = wc_edostavka_get_location_cities( array(
				'code' => $code,
				'size' => 1,
				'lang' => isset( $_POST['lang'] ) && in_array( $_POST['lang'], array(
					'eng',
					'rus'
				), true ) ? $_POST['lang'] : wc_edostavka_get_locale()
			) );

			if ( $location && isset( $location[0] ) && $code == $location[0]->code ) {

				try {

					$location_props = array(
						'country_code' => $location[0]->country_code,
						'region_code'  => $location[0]->region_code,
						'region'       => $location[0]->region,
						'city_code'    => $code,
						'city'         => $location[0]->city,
						'longitude'    => $location[0]->longitude,
						'latitude'     => $location[0]->latitude
					);

					$customer_location = wc_edostavka_shipping()->get_customer_handler();
					$customer_location->set_location( $location_props );

					wp_send_json_success( $customer_location->get_location() );
					//$customer_location->save();

				} catch ( Exception $e ) {
					wc_edostavka_shipping()->log( $e->getMessage() );
				}

			} else {
				wp_send_json_error( sprintf( 'Не удалось получить информацию о населённом пункте по коду %d.', $code ) );
			}
		}

		wp_send_json_error( 'Во время запроса произошла неизвестная ошибка.' );
	}

	public function set_delivery_point() {

		try {

			$post = $_POST;

			if ( ! empty( $post ) && isset( $post['code'] ) ) {

				wp_send_json_success( wc_edostavka_set_delivery_point_data( array_filter( array_map( 'wc_clean', $post ) ) ) );

			} else {
				throw new Woodev_Plugin_Exception( 'Не переданы обязательные параметры.' );
			}

		} catch ( Woodev_Plugin_Exception $e ) {

			wp_send_json_error( $e->getMessage() );
		}
	}

	public function order_actions() {

		if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'edostavka-order-action' ) && isset( $_GET['make'], $_GET['item_id'] ) ) {
			$make  = sanitize_text_field( wp_unslash( $_GET['make'] ) );
			$order = new WC_Edostavka_Shipping_Order( absint( wp_unslash( $_GET['item_id'] ) ) );

			$message_handler = wc_edostavka_shipping()->get_message_handler();

			switch ( $make ) {
				case 'export' :
					if ( $order->export_action() ) {
						$message_handler->add_message( sprintf( __( 'The order %d was successfully exported to CDEK', 'woocommerce-edostavka' ), $order->get_order_number() ) );
					} else {
						$message_handler->add_error( sprintf( __( 'Failed to export orders to CDEK. You can see the reason in the <a href="%s" target="_blank">notes to the order</a>.', 'woocommerce-edostavka' ), esc_url( $order->get_edit_order_url() ) ) );
					}

					break;
				case 'cancel' :
					if ( $order->cancel_action() ) {
						$message_handler->add_message( sprintf( __( 'The order %d was successfully canceled', 'woocommerce-edostavka' ), $order->get_order_number() ) );
					} else {
						$message_handler->add_error( __( 'Failed to cancel orders from CDEK.', 'woocommerce-edostavka' ) );
					}

					break;
				case 'update' :
					if ( $order->update_order() ) {
						$message_handler->add_message( sprintf( __( 'Information of order %d was updated', 'woocommerce-edostavka' ), $order->get_order_number() ) );
					}
					break;
			}
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : add_query_arg( 'page', 'wc_edostavka_orders', admin_url( 'admin.php' ) ) );
		exit;
	}

	public function create_courier_call() {

		try {
			if ( ! check_admin_referer( 'wc-edostavka-orders-table', 'security' ) ) {
				throw new Woodev_API_Exception( 'Bad security nonce' );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Woodev_API_Exception( 'You have not permissions to this action.' );
			}

			$required_fields = array( 'orders', 'date', 'time_from', 'time_to' );

			foreach ( $required_fields as $field ) {
				if ( empty( $_POST[ $field ] ) ) {
					throw new Woodev_API_Exception( __( 'Need fill all required fields.', 'woocommerce-edostavka' ) );
				}
			}

			if ( strtotime( $_POST['date'] ) < time() ) {
				throw new Woodev_API_Exception( __( 'The date of intake couldn\'t be less than a current date.', 'woocommerce-edostavka' ) );
			}

			$time_start = 9 * HOUR_IN_SECONDS;
			$time_end   = 22 * HOUR_IN_SECONDS;

			list( $time_from_hours, $time_from_minutes ) = explode( ':', $_POST['time_from'] );
			$time_from = $time_from_hours * HOUR_IN_SECONDS + $time_from_minutes;

			list( $time_to_hours, $time_to_minutes ) = explode( ':', $_POST['time_to'] );
			$time_to = $time_to_hours * HOUR_IN_SECONDS + $time_to_minutes;

			if ( $time_from < $time_start ) {
				throw new Woodev_API_Exception( __( 'Time to start awaiting a courier must be more or equal 09:00', 'woocommerce-edostavka' ) );
			} elseif ( $time_from > $time_end - 60 ) {
				throw new Woodev_API_Exception( __( 'Time to start awaiting a courier must be less or equal 21:59', 'woocommerce-edostavka' ) );
			}

			if ( $time_to < $time_start + 60 ) {
				throw new Woodev_API_Exception( __( 'Time to end awaiting a courier must be more or equal 09:01', 'woocommerce-edostavka' ) );
			} elseif ( $time_to > $time_end ) {
				throw new Woodev_API_Exception( __( 'Time to end awaiting a courier must be less or equal 22:00', 'woocommerce-edostavka' ) );
			}

			if ( $time_to <= $time_from ) {
				throw new Woodev_API_Exception( __( 'Time to end awaiting a courier can not be equal or less than time to start awaiting.', 'woocommerce-edostavka' ) );
			}

			$order_ids       = ( array ) $_POST['orders'];
			$results         = array();
			$message_handler = wc_edostavka_shipping()->get_message_handler();

			foreach ( $order_ids as $order_id ) {

				$order = new WC_Edostavka_Shipping_Order( $order_id );

				/** @var WD_Edostavka_Shipping_API_Response $response */
				$response = wc_edostavka_shipping()->get_api()->create_call_courier( array(
					'order_uuid'       => $order->get_order_meta( 'cdek_order_id' ),
					'intake_date'      => wc_clean( $_POST['date'] ),
					'intake_time_from' => wc_clean( $_POST['time_from'] ),
					'intake_time_to'   => wc_clean( $_POST['time_to'] ),
					'comment'          => esc_textarea( $_POST['comment'] )
				) );

				$order->delete_meta_data( '_wc_edostavka_can_courier_call' );
				$order->update_order_meta( 'courier_already_called', $response->get_entity_uuid(), false );
				$order->save_meta_data();

				$results['results'][] = $response->get_response_data();
			}

			if ( count( $order_ids ) == count( $results['results'] ) ) {
				$message_handler->add_message( sprintf( __( 'A courier was called on %s', 'woocommerce-edostavka' ), wp_date( wc_date_format(), strtotime( $_POST['date'] ) ), new DateTimeZone( wc_timezone_string() ) ) );
			} elseif ( count( $results['results'] ) < count( $order_ids ) ) {
				$message_handler->add_warning( sprintf( __( 'A courier was called only to %d from %d orders.', 'woocommerce-edostavka' ), count( $results['results'] ), count( $order_ids ) ) );
			}

			if ( $message_handler->message_count() > 0 || $message_handler->warning_count() > 0 ) {
				$results['redirect'] = $message_handler->redirect( add_query_arg( 'page', 'wc_edostavka_orders', admin_url( 'admin.php' ) ), false );
			}

			wp_send_json_success( $results );

		} catch ( Woodev_API_Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	public function get_order_details() {

		try {

			if ( ! check_admin_referer( 'wc-edostavka-preview-order', 'security' ) ) {
				throw new Woodev_Plugin_Exception( 'Bad security nonce' );
			}

			if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_GET['order_id'] ) ) {
				throw new Woodev_Plugin_Exception( 'You have not permissions to this action or not exists require params.' );
			}

			$order = new WC_Edostavka_Shipping_Order( absint( $_GET['order_id'] ) );

			$countries        = WC()->countries->get_countries();
			$order_total_cost = number_format( $order->get_total(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );

			wp_send_json_success( apply_filters( 'wc_edostavka_order_preview_details', array(
				'data'          => $order->get_data(),
				'order_number'  => $order->get_order_number(),
				'status'        => strtolower( $order->get_order_status() ),
				'status_name'   => $order->get_order_status_name(),
				'customer_name' => $order->get_formatted_billing_full_name(),
				'address'       => implode( ', ', array_filter( array(
					$countries[ $order->get_billing_country() ],
					$order->get_billing_postcode(),
					$order->get_billing_state(),
					$order->get_billing_city(),
					$order->get_billing_address_1()
				) ) ),
				'payment_via'   => $order->get_payment_method_title(),
				'payment_cost'  => sprintf( '%s %s', 'cod' === $order->get_payment_method() ? $order_total_cost : 0, $order->get_currency() ),
				'shipping_data' => $order->get_order_shipping_data(),
				'cdek_number'   => wc_edostavka_get_tracking_code( $order ),
				'wc_status'     => wc_get_order_status_name( $order->get_status() )
			), $order ) );

		} catch ( Woodev_Plugin_Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
}

return new WD_Edostavka_Shipping_AJAX;
