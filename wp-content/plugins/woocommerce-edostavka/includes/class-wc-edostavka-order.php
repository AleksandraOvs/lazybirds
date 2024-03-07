<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Shipping_Order extends WC_Order {

	public function __construct( $order ) {
		parent::__construct( $order );

		add_filter( 'woocommerce_edostavka_order_number', array( $this, 'set_order_prefix_number' ) );
	}

	public function has_edostavka_shipping() {

		foreach( $this->get_shipping_methods() as $item ) {

			if( $item->get_method_id() == wc_edostavka_shipping()->get_method_id() ) {
				return true;
			}
		}

		return false;
	}

	public function set_order_prefix_number( $number ) {
		$order_prefix = wc_edostavka_shipping()->get_integration_handler()->get_option( 'order_prefix' );
		if( ! empty( $order_prefix ) ) {
			$number = sprintf( '%s%s', $order_prefix, $number );
		}
		return $number;
	}

	public function get_order_status() {
		return $this->get_order_meta( 'status' );
	}

	public function set_order_status( $status ) {

		$status = strtoupper( $status );
		$old_status = strtoupper( $this->get_order_status() );
		$all_statuses = wc_edostavka_get_order_statuses();

		if( in_array( $status, array_keys( $all_statuses ), true ) && $old_status !== $status ) {

			$this->update_order_meta( 'status', $status );
			$need_create_notice = apply_filters( 'wc_edostavka_order_need_create_notice', in_array( $status, apply_filters( 'wc_edostavka_order_statuses_to_create_notice', array_keys( $all_statuses ), $status ), true ), $this );
			if( $old_status ) {

				if( $need_create_notice ) {
					$this->add_order_note( sprintf( __( 'The order status of CDEK was change from %s to %s', 'woocommerce-edostavka' ), $all_statuses[ $old_status ], $all_statuses[ $status ] ) );
				}

				do_action( 'wc_edostavka_order_status_changed_from_' . strtolower( $old_status ) . '_to' . strtolower( $status ), $this->get_id() );
			} else {

				if( $need_create_notice ) {
					$this->add_order_note( sprintf( __( 'The order status of CDEK was change to %s', 'woocommerce-edostavka' ), $all_statuses[ $status ] ) );
				}

				do_action( 'wc_edostavka_order_status_changed_to_' . strtolower( $status ), $this->get_id() );
			}

			do_action( 'wc_edostavka_order_status_changed', strtolower( $old_status ), strtolower( $status ), $this->get_id() );
		}
	}

	public function is_exported() {

		if( ! empty( $this->get_order_meta( 'cdek_order_id' ) ) && ! in_array( strtoupper( $this->get_order_status() ), array( 'CANCELED', 'INVALID' ), true ) ) {
			return true;
		}

		return false;
	}

	public function export_action() {

		try {

			$order_factory = new WC_Edostavka_Order_Factory( $this );

			$notice_messages = array();

			$order_params = array(
				'number'                    => apply_filters( 'woocommerce_edostavka_order_number', $this->get_order_number(), $this ),
				'developer_key'             => WC_CDEK_SHIPPING_DEVELOPER_KEY,
				'date_invoice'              => date('Y-m-d' ),
				'tariff_code'               => intval( $order_factory->get_tariff_code() ),
				'recipient'                 => $order_factory->get_recipient(),
				'sender'                    => $order_factory->get_sender(),
				'seller'                    => $order_factory->get_seller(),
				'delivery_recipient_cost'   => array( 'value' => $order_factory->get_delivery_cost() ),
				'comment'                   => esc_textarea( wp_encode_emoji( $this->get_customer_note() ) ),
				'packages'                  => $order_factory->get_packages()
			);

			$shipper_name = wc_edostavka_shipping()->get_integration_handler()->get_option( 'sender_name' );
			$shipper_address = wc_edostavka_shipping()->get_integration_handler()->get_option( 'sender_address' );

			if( ! empty( $shipper_name ) ) {
				$order_params['shipper_name'] = esc_attr( $shipper_name );
			}

			if( ! empty( $shipper_address ) ) {
				$order_params['shipper_address'] = esc_attr( $shipper_address );
			}

			$edit_method_url = add_query_arg( array(
				'page'          => 'wc-settings',
				'tab'           => 'shipping',
				'instance_id'   => $order_factory->get_shipping_item()->get_instance_id()
			), admin_url( 'admin.php' ) );

			if( $order_factory->get_tariff_from() == 'stock' ) {

				$shipment_point = $order_factory->get_shipping_method()->get_option( 'delivery_point' );

				if( ! empty( $shipment_point ) ) {
					$order_params['shipment_point'] = $shipment_point;
				} else {
					throw new Woodev_API_Exception( sprintf( __( 'Shipment point is not set on <a href="%s" target="_blank">method settings</a>.', 'woocommerce-edostavka' ), esc_url( $edit_method_url ) ) );
				}

			} elseif( $order_factory->get_tariff_from() == 'door' ) {

				$sender_city = $order_factory->get_shipping_method()->get_option( 'sender_city' );
				$dropoff_address = $order_factory->get_shipping_method()->get_option( 'dropoff_address' );

				if( ! empty( $sender_city ) ) {
					$order_params['from_location']['code'] = intval( $sender_city );
				} else {
					throw new Woodev_API_Exception( sprintf( __( 'Sender city is not set on <a href="%s" target="_blank">method settings</a>.', 'woocommerce-edostavka' ), esc_url( $edit_method_url ) ) );
				}

				if( ! empty( $dropoff_address ) ) {
					$order_params['from_location']['address'] = esc_attr( $dropoff_address );
				} else {
					throw new Woodev_API_Exception( sprintf( __( 'Your address to drop order is not set on <a href="%s" target="_blank">method settings</a>.', 'woocommerce-edostavka' ), esc_url( $edit_method_url ) ) );
				}
			}

			if( in_array( $order_factory->get_tariff_to(), array( 'stock', 'postamat' ), true ) ) {

				$chosen_delivery_point = $this->get_order_meta( 'chosen_delivery_point' );

				if( $chosen_delivery_point && isset( $chosen_delivery_point[0], $chosen_delivery_point[0]['code'] ) ) {
					$order_params['delivery_point'] = $chosen_delivery_point[0]['code'];
					/**
					 * В случае если у нас постамат "халва" то принудительно устанавливаем метод упаковки в "Упаковывать все товары в одну коробку"
					 */
					if( 'postamat' == $order_factory->get_tariff_to() && isset( $chosen_delivery_point[0]['owner_code'] ) && wc_strtolower( $chosen_delivery_point[0]['owner_code'] ) == 'халва' ) {
						$packing_method = wc_edostavka_shipping()->get_integration_handler()->get_option( 'packing_method', 'per_item' );

						if( 'single_box' !== $packing_method ) {
							$order_factory->set_packing_method( 'single_box' );
							$order_params['packages'] = $order_factory->get_packages();

							$packing_methods = wc_edostavka_get_box_packing_methods();

							$notice_messages[] = sprintf( 'Метод упаковки товаров для данного заказа был автоматически изменён с "%s" на "%s", так как постаматы "Халва" принимают товары только в одной упаквке.', $packing_methods[ $packing_method ], $packing_methods[ 'single_box' ] );
						}
					}
				} else {
					throw new Woodev_API_Exception( __( 'Delivery point is not set to this order.', 'woocommerce-edostavka' ) );
				}

			} elseif ( $order_factory->get_tariff_to() == 'door' ) {

				$customer_location = $this->get_order_customer_location();
				$billing_address = $this->get_billing_address_1();

				$order_params['to_location']['code'] = intval( $customer_location['city_code'] );

				if( ! empty( $billing_address ) ) {
					$order_params['to_location']['address'] = esc_attr( $billing_address );
				} else {
					throw new Woodev_API_Exception( __( 'Delivery address is not set to this order.', 'woocommerce-edostavka' ) );
				}

				if( $this->get_billing_postcode() ) {
					$order_params['to_location']['postal_code'] = wc_format_postcode( $this->get_billing_postcode(), $this->get_billing_country() );
				}

				foreach ( array( 'country_code', 'region_code', 'region', 'city', 'longitude', 'latitude' ) as $field ) {
					if( ! empty( $customer_location[ $field ] ) ) {
						$order_params['to_location'][ $field ] = $customer_location[ $field ];
					}
				}
			}

			$services = $order_factory->get_shipping_method()->get_option( 'services', array() );
			$service_loop = 0;

			if( ! empty( $services ) ) {

				foreach ( $services as $service ) {
					if( 'INSURANCE' === $service || ( 'PART_DELIV' === $service && $this->get_item_count() == 1 ) ) continue;

					$order_params['services'][ $service_loop ]['code'] = $service;

					if( 'SMS' === $service && $this->get_billing_phone() ) {
						$order_params['services'][ $service_loop ]['parameter'] = wc_format_phone_number( $this->get_billing_phone() );
					}

					$service_loop++;
				}
			}

			$edostavka_rate_data = $order_factory->get_shipping_item()->get_meta( 'edostavka_rate' );

			if( isset( $edostavka_rate_data['services'] ) && ! empty( $edostavka_rate_data['services'] ) ) {
				foreach ( ( array ) $edostavka_rate_data['services'] as $service ) {

					if( ! in_array( $service->code, array_keys( wc_edostavka_get_carton_boxes() ), true ) ) continue;

					$order_params['services'][ $service_loop ] = array(
						'code'      => $service->code,
						'parameter' => count( wp_list_filter( $order_params['packages'], array( 'key' => $service->code ) ) )
					);

					$service_loop++;
				}
			}

			$order_params = apply_filters( 'wc_edostavka_export_order_api_params', $order_params, $this );

			/** @var WD_Edostavka_Shipping_API_Response $order_result */
			$order_result = wc_edostavka_shipping()->get_api()->create_order( $order_params );
			$entity_id     = $order_result->get_entity_uuid();

			if( $entity_id ) {

				$this->update_order_meta( 'cdek_order_id', $entity_id, false );
				$this->set_order_status( 'ACCEPTED' );

				if( $order_result->get_requests() && is_array( $order_result->get_requests() ) ) {

					foreach ( $order_result->get_requests() as $request ) {
						if( $request->type == 'CREATE' && in_array( $request->state, array( 'ACCEPTED', 'WAITING', 'SUCCESSFUL' ), true ) ) {

							//TODO: Этот костыль нужен для того, что успеть получить более менее актуальную информацию. СДЭК долго обрабатвает запрос на создание заказа.
							sleep( 1 );
							break;
						}
					}
				}

				foreach ( $notice_messages as $note ) {
					$this->add_order_note( $note );
				}

				$this->update_order();
			}

		} catch ( Woodev_API_Exception $e ) {

			$this->add_order_note( sprintf( __( 'Error occurred in process export the order: %s', 'woocommerce-edostavka' ), $e->getMessage() ) );

			$this->set_order_status( 'INVALID' );

			$this->add_log_message( sprintf( __( 'Failed to export the order %s. Error: %s', 'woocommerce-edostavka' ), $this->get_order_number(), $e->getMessage() ) );

			return false;
		}

		return true;
	}

	public function update_order() {

		if( $this->is_exported() ) {

			try {

				/** @var WD_Edostavka_Shipping_API_Response $remote_order */
				$remote_order = wc_edostavka_shipping()->get_api()->get_order( $this->get_order_meta( 'cdek_order_id' ) );
				$remote_order_data = $remote_order->get_response_data();

				$history_statuses = $remote_order->get_history_statuses();

				do_action( 'wc_edostavka_before_order_update', $this );

				if( ! empty( $history_statuses ) ) {

					/*
					 * Тут нам приходится сортировать массив $history_statuses так как СДЭК может вернуть одинаковые даты
					 * и при этом статус CREATED будет элементом массива ниже
					 * Суть сортировки, что бы CREATED был всегда выше в массиве чем ACCEPTED
					 */
					usort( $history_statuses, function ( $a, $b ) {
						if( strtotime( $a->date_time ) === strtotime( $b->date_time ) ) {
							return strcasecmp( $b->code, $a->code );
						}

						return ( strtotime( $a->date_time ) > strtotime( $b->date_time ) ) ? -1 : 1;
					} );

					if( $history_statuses[0]->code !== $this->get_order_status() && in_array( strtoupper( $history_statuses[0]->code ), array_keys( wc_edostavka_get_order_statuses() ), true ) ) {
						$current_status_code = strtoupper( $history_statuses[0]->code );
						$this->set_order_status( $current_status_code );

						$status_delivered = wc_edostavka_shipping()->get_integration_handler()->get_option( 'status_delivered', 'wc-completed' );

						if( 'DELIVERED' == $current_status_code && wc_is_order_status( $status_delivered ) ) {
							$status_slug = ( 'wc-' === substr( $status_delivered, 0, 3 ) ) ? substr( $status_delivered, 3 ) : $status_delivered;
							$this->update_status( $status_slug, sprintf( __( 'The order was marked as "%s" automatically, because it delivered to recipient.', 'woocommerce-edostavka' ), wc_get_order_status_name( $status_slug ) ), true );

							do_action( 'wc_edostavka_order_mark_as_delivered', $this->get_id(), $status_slug );
						}

						do_action( sprintf( 'wc_edostavka_order_%s_notification', wc_strtolower( $current_status_code ) ), $this->get_id(), $this );
					}

					$this->update_order_meta( 'history_statuses', array_filter( $history_statuses, function ( $status ) {
						return in_array( $status->code, wc_edostavka_get_status_keys_for_show(), true );
					} ), false );
				}

				if( ! empty( $remote_order_data->entity->cdek_number ) && $this->get_order_meta( 'tracking_code' ) !== $remote_order_data->entity->cdek_number ) {
					wc_edostavka_update_tracking_code( $this, $remote_order_data->entity->cdek_number );
				}

				if( ! empty( $remote_order_data->entity->delivery_mode ) && in_array( $remote_order_data->entity->delivery_mode, array( '1', '2', '6' ), true ) ) {
					$this->update_order_meta( 'can_courier_call', true, false );
				}

				$this->update_order_meta( 'latest_order_update_time', time(), false );

				do_action( 'wc_edostavka_before_order_update_save_meta', $this );

				$this->save_meta_data();
				$this->save();

				do_action( 'wc_edostavka_after_order_update', $this );

			} catch ( Woodev_API_Exception $e ) {

				$this->add_log_message( sprintf( __( 'Failed to get remote order data. Error: %s', 'woocommerce-edostavka' ), $e->getMessage() ) );

				return false;
			}

		}

		return true;
	}

	public function cancel_action() {

		if( $this->is_exported() ) {

			try {

				wc_edostavka_shipping()->get_api()->remove_order( $this->get_order_meta( 'cdek_order_id' ) );

				$this->set_order_status( 'CANCELED' );
				$this->add_order_note( sprintf( __( 'The order #%s was cancel by manager.', 'woocommerce-edostavka' ), $this->get_id() ) );
				$this->update_order_meta( 'waybill_downloaded', false, false );
				$this->update_order_meta( 'barcode_downloaded', false );

			} catch ( Woodev_API_Exception $e ) {

				$this->add_log_message( sprintf( __( 'Failed to remove the order %s from CDEK. Error: %s', 'woocommerce-edostavka' ), $this->get_order_number(), $e->getMessage() ) );

				return false;
			}
		}

		return true;
	}

	public function is_editable() {
		$editable = parent::is_editable();
		return apply_filters( 'wc_edostavka_order_is_editable', ( $editable && in_array( strtoupper( $this->get_order_status() ), array( 'NEW', 'ACCEPTED', 'CREATED', 'CANCELED', 'INVALID' ), true ) ), $this );
	}

	public function is_delivered() {
		return apply_filters( 'wc_edostavka_order_is_delivered', in_array( strtoupper( $this->get_order_status() ), array( 'DELIVERED', 'NOT_DELIVERED' ), true ), $this );
	}

	public function get_order_status_name( $status = null ) {

		if( is_null( $status ) ) {
			$status = $this->get_order_status();
		}

		$status = strtoupper( $status );

		if( ! empty( $status ) ) {
			$statuses = wc_edostavka_get_order_statuses();
			if( in_array( $status, array_keys( $statuses ), true ) ) {
				return $statuses[ $status ];
			}
		}

		return __( 'N/A', 'woocommerce-edostavka' );
	}

	/**
	 * Update meta data by key, if provided.
	 *
	 * @param  string       $meta_key Meta key.
	 * @param  string|array $meta_value Meta value.
	 * @param  bool         $save Whether to delete keys from DB right away. Could be useful to pass `false` if you are building a bulk request.
	 */
	public function update_order_meta( $meta_key, $meta_value, $save = true ) {

		if ( ! $meta_key || ! $meta_value ) {
			return;
		}

		$this->update_meta_data( "_wc_edostavka_{$meta_key}", $meta_value );

		if ( $save ) {
			$this->save_meta_data();
		}
	}

	/**
	 * Get Meta Data by Key.
	 *
	 * @param  string $meta_key Meta Key.
	 * @param  bool   $single return first found meta with key, or all with $key.
	 * @return mixed
	 */
	public function get_order_meta( $meta_key, $single = true ) {
		return $this->get_meta( "_wc_edostavka_{$meta_key}", $single );
	}

	public function get_order_customer_location() {
		return $this->get_order_meta( 'customer_location' );
	}

	public function get_order_shipping_data() {
		$data = $this->get_order_meta( 'shipping' );

		if( $data ) {
			return $data;
		}

		return false;
	}

	public function add_log_message( $message ) {
		if( ! empty( $message ) ) {
			wc_edostavka_shipping()->log( $message, sprintf( '%s_orders', wc_edostavka_shipping()->get_method_id() ) );
		}
	}
}
