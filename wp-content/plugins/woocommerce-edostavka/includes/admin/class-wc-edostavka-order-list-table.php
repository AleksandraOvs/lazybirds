<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Edostavka_Order_List_Table extends WP_List_Table {

	/**
	 * Set of available views can include (All, Active, Completed, Cancelled, Trash)
	 *
	 * @var array
	 */
	private $views;

	function __construct(){

		parent::__construct(
			array(
				'singular' => __( 'CDEK Order', 'woocommerce-edostavka' ),
				'plural'   => __( 'CDEK Orders', 'woocommerce-edostavka' ),
				'ajax'     => false
			)
		);

		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'handle_query_var' ), 10, 2 );
	}

	public function get_bulk_actions() {

		return array(
			'export'    => __( 'Export to CDEK', 'woocommerce-edostavka' ),
			'update'    => __( 'Update', 'woocommerce-edostavka' ),
			'cancel'    => __( 'Cancel', 'woocommerce-edostavka' )
		);
	}

	public function get_views() {
		if ( ! isset( $this->views ) ) {
			$this->views = array();

			$statuses = array(
				'all'       => __( 'All orders', 'woocommerce-edostavka' ),
				'new'       => __( 'New', 'woocommerce-edostavka' ),
				'accepted'  => __( 'Accepted', 'woocommerce-edostavka' ),
				'created'   => __( 'Created', 'woocommerce-edostavka' ),
				'delivered' => __( 'Delivered', 'woocommerce-edostavka' ),
				'canceled'  => __( 'Canceled', 'woocommerce-edostavka' ),
			);

			$status_counts = wc_edostavka_get_counts_order_status();

			$counts = array( 'all' => 0 );

			foreach ( $status_counts as $row ) {
				$counts[ strtolower( $row->status ) ] = $row->count;
				$counts['all'] += $row->count;
			}

			foreach ( $statuses as $status_key => $status_name ) {

				$current_status = isset( $_GET['edostavka_order_status'] ) ? $_GET['edostavka_order_status'] : 'all';
				$classes = array();
				$count = isset( $counts[ $status_key ] ) ? $counts[ $status_key ] : 0;

				if( $status_key == $current_status ) {
					$classes[] = 'current';
				}

				$base_url = admin_url( 'admin.php?page=wc_edostavka_orders' );
				if ( isset( $_REQUEST['s'] ) ) $base_url = add_query_arg( 's', $_REQUEST['s'], $base_url );

				$this->views[ $status_key ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'edostavka_order_status', $status_key, $base_url ), implode( ' ', $classes ), ucfirst( $status_name ), $count );
			}

		}

		return $this->views;
	}

	public function get_sortable_columns() {

		return array(
			'order_date' => array( 'post_date', false ),  // false because the inital sort direction is DESC so we want the first column click to sort ASC
			'order'      => array( 'ID', false ),         // same logic as order_date
		);
	}

	public function get_columns() {
		return array(
			'cb'            => '<input type="checkbox" />',
			'order'         => __( 'Order', 'woocommerce-edostavka' ),
			'status'        => __( 'Status', 'woocommerce-edostavka' ),
			'customer'      => __( 'Customer', 'woocommerce-edostavka' ),
			'delivery'      => __( 'Delivery', 'woocommerce-edostavka' ),
			'payment'       => __( 'Payment', 'woocommerce-edostavka' ),
			'cdek_number'   => __( 'Tracking number', 'woocommerce-edostavka' ),
			'order_date'    => __( 'Order Date', 'woocommerce-edostavka' ),
			'actions'       => __( 'Actions', 'woocommerce-edostavka' ),
		);
	}

	/**
	 * Get content for the special checkbox column
	 *
	 * @see WP_List_Table::single_row_columns()
	 * @param WC_Edostavka_Shipping_Order $item one row (item) in the table
	 * @return string the checkbox column content
	 */
	function column_cb( $item ){
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', 'order_id', $item->get_id() );
	}

	/**
	 * Get column content, this is called once per column, per row item ($order)
	 * returns the content to be rendered within that cell.
	 *
	 * @see WP_List_Table::single_row_columns()
	 * @param WC_Edostavka_Shipping_Order $item one row (item) in the table
	 * @param string $column_name the column slug
	 * @return string the column content
	 */
	public function column_default( $item, $column_name ) {

		$column_content = '';

		switch ( $column_name ) {

			case 'customer':

				$customer_name = $item->get_formatted_billing_full_name();
				$billing_email = $item->get_billing_email();
				$user_id = $item->get_customer_id();

				if( ! $customer_name ) {
					$user  = get_user_by( 'id', $user_id );
					$customer_name = ucwords( $user->display_name );
				}


				if ( 0 !== $user_id ) {
					$column_content = sprintf( '<a href="%s">%s (%s)</a>', get_edit_user_link( $user_id ), $customer_name, $billing_email );
				} else {
					$column_content = sprintf( '<span>%s <br />%s</span>', $customer_name, $billing_email );
				}

				if( $item->get_billing_phone() ) {
					$column_content .= sprintf( '<span class="description dashicons-before dashicons-phone"> %s</span>', wc_format_phone_number( $item->get_billing_phone() ) );
				}

				break;

			case 'delivery':

				$column_content = sprintf( __( 'Address: %s', 'woocommerce-edostavka' ), esc_html( implode(', ', array_filter( array(
					$item->get_billing_postcode(),
					$item->get_billing_state(),
					$item->get_billing_city(),
					$item->get_billing_address_1()
				) ) ) ) );

				$column_content .= sprintf( '<span class="description">%s: %s</span>', $item->get_shipping_method(), wp_kses_post( wc_price( $item->get_shipping_total() ) ) );

				break;

			case 'payment':

				$column_content = sprintf( '<span>%s: %s</span>', $item->get_payment_method_title(), $item->get_formatted_order_total() );
				if( ! $item->is_exported() && $item->needs_payment() ) {
					$column_content .= '<span class="description not-paid">Заказ может быть неоплаченым</span>';
				}

				break;

			case 'cdek_number':

				$tracking_number = $item->get_order_meta( 'tracking_code' );

				if( $tracking_number ) {
					$column_content = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( add_query_arg( array( 'order_id' => $tracking_number ), 'https://www.cdek.ru/ru/tracking' )), $tracking_number );
					if( $item->get_order_meta( 'can_courier_call' ) && ! $item->get_order_meta( 'courier_already_called' ) ) {
						$column_content .= '<span class="description">Необходимо вызвать курьера</span>';
					}
				} else {
					$column_content = '&ndash;';
				}

				break;

		}

		return $column_content;
	}

	/**
	 * @param WC_Edostavka_Shipping_Order $item
	 *
	 * @return string
	 */
	public function column_order( $item ) {

		if( 'trash' === $item->get_status() ) {
			return sprintf( '<strong>%s</strong>', sprintf( __( 'Order %s', 'woocommerce-edostavka' ), $item->get_order_number() ) );
		} else {

			$links = array();

			$links['preview_order'] = sprintf(
				'<a href="#" class="order-preview" data-order-id="%s" title="%s">%s</a>',
				absint( $item->get_id() ),
				esc_html__( 'Preview this order', 'woocommerce-edostavka' ),
				esc_html__( 'Preview', 'woocommerce-edostavka' )
			);

			$links['edit'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				add_query_arg(
					array(
						'view'      => 'orders',
						'order_id'  => $item->get_id(),
					),
					$item->get_edit_order_url()
				),
				esc_html__( 'Edit this order', 'woocommerce-edostavka' ),
				sprintf( esc_html__( 'Order %s', 'woocommerce-edostavka' ), $item->get_order_number() )
			);

			return implode( ' ', array_values( $links ) );

			//return sprintf( '<a href="%s">%s</a>%s', get_edit_post_link( $item->get_id() ), sprintf( __( 'Order %s', 'woocommerce-edostavka' ), $item->get_order_number() ), $this->row_actions( $actions ) );
		}
	}

	/**
	 * @param WC_Edostavka_Shipping_Order $item
	 *
	 * @return string
	 */
	public function column_status( $item ) {
		return sprintf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-cdek-' . strtolower( $item->get_order_status() ) ) ), esc_html( $item->get_order_status_name() ) );
	}

	public function column_order_date( $item ) {

		$order_timestamp = $item->get_date_created() ? $item->get_date_created()->getTimestamp() : '';

		if ( ! $order_timestamp ) {
			echo '&ndash;';
			return;
		}

		if ( $order_timestamp > strtotime( '-1 day', time() ) && $order_timestamp <= time() ) {
			$show_date = sprintf(
				_x( '%s ago', '%s = human-readable time difference', 'woocommerce' ),
				human_time_diff( $item->get_date_created()->getTimestamp(), time() )
			);
		} else {
			$show_date = $item->get_date_created()->date_i18n( __( 'j M Y', 'woocommerce' ) );
		}

		return sprintf(
			'<time datetime="%1$s" title="%2$s">%3$s</time>',
			esc_attr( $item->get_date_created()->date( 'c' ) ),
			esc_html( $item->get_date_created()->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
			esc_html( $show_date )
		);
	}

	/**
	 * @param WC_Edostavka_Shipping_Order $item
	 *
	 * @return void
	 */
	public function column_actions( $item ) {
		printf( '<p>%s</p>', wc_render_action_buttons( $this->get_order_actions( $item ) ) );
	}

	public function get_order_actions( $order_item ) {
		$actions = array();

		if( $order_item instanceof WC_Edostavka_Shipping_Order ) {

			$action_base_url = add_query_arg( array(
				'action'    => 'edostavka_order_action',
				'item_id'   => $order_item->get_id(),
				'make'      => ''
			), admin_url( 'admin-ajax.php' ) );

			if( ! $order_item->is_exported() && $order_item->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {

				$actions['export'] = array(
					'url'    => wp_nonce_url( add_query_arg( 'make', 'export', $action_base_url ), 'edostavka-order-action' ),
					'name'   => __( 'Export to CDEK', 'woocommerce-edostavka' ),
					'action' => 'edostavka-export',
				);

			} elseif( $order_item->is_exported() ) {

				$actions['update'] = array(
					'url'    => wp_nonce_url( add_query_arg( 'make', 'update', $action_base_url ), 'edostavka-order-action' ),
					'name'   => __( 'Update order inform', 'woocommerce-edostavka' ),
					'action' => 'edostavka-update',
				);

				if( ! in_array( strtoupper( $order_item->get_order_status() ), array( 'NEW', 'ACCEPTED', 'INVALID', 'CANCELED' ), true ) ) {

					$actions['waybill'] = array(
						'url'    => wp_nonce_url( add_query_arg( array( 'action' => 'download_order_file', 'file_type' => 'waybill', 'order_id' => $order_item->get_id() ), admin_url( 'admin.php' ) ), 'order-file-export', 'nonce' ),
						'name'   => __( 'Get a waybill', 'woocommerce-edostavka' ),
						'action' => $order_item->get_order_meta( 'waybill_downloaded' ) ? 'edostavka-waybill-downloaded' : 'edostavka-waybill',
					);

					$actions['barcode'] = array(
						'url'    => wp_nonce_url( add_query_arg( array( 'action' => 'download_order_file', 'file_type' => 'barcode', 'order_id' => $order_item->get_id() ), admin_url( 'admin.php' ) ), 'order-file-export', 'nonce' ),
						'name'   => __( 'Get a barcode', 'woocommerce-edostavka' ),
						'action' => $order_item->get_order_meta( 'barcode_downloaded' ) ? 'edostavka-barcode-downloaded' : 'edostavka-barcode',
					);
				}

				$actions['cancel'] = array(
					'url'    => wp_nonce_url( add_query_arg( 'make', 'cancel', $action_base_url ), 'edostavka-order-action' ),
					'name'   => __( 'Cancel', 'woocommerce-edostavka' ),
					'action' => 'edostavka-cancel',
				);
			}
		}

		return $actions;
	}

	//public function advanced_filters() {}

	private function get_current_orderby() {
		return isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'post_date';
	}

	private function get_current_order() {
		return isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
	}

	/**
	 * @param array $query - Args for WP_Query.
	 * @param array $query_vars - Query vars from WC_Order_Query.
	 * @return array modified $query
	 */
	public function handle_query_var( $query, $query_vars ) {

		if( ! empty( $query_vars['is_edostavka'] ) ) {
			$query['meta_query'][] = array(
				'key'       => '_wc_edostavka_shipping',
				'compare'   => 'EXISTS'
			);
		}

		if( ! empty( $query_vars['edostavka_status'] ) ) {
			$query['meta_query'][] = array(
				'key'       => '_wc_edostavka_status',
				'value'     => strtoupper( $query_vars['edostavka_status'] )
			);
		}

		if( ! empty( $query_vars['is_courier'] ) ) {

			$query['meta_query'][] = array(
				'key'       => '_wc_edostavka_cdek_order_id'
			);

			$query['meta_query'][] = array(
				'key'       => '_wc_edostavka_status',
				'value'     => array( 'ACCEPTED', 'CREATED' )
			);

			$query['meta_query'][] = array(
				'key'       => '_wc_edostavka_can_courier_call'
			);

			$query['meta_query'][] = array(
				'key'       => '_wc_edostavka_courier_already_called',
				'compare'   => 'NOT EXISTS'
			);
		}

		return $query;
	}

	protected function extra_tablenav( $which ) {
		if ( 'top' === $which && count( $this->get_orders_to_call_courier() ) > 0 ) {
			echo '<div class="alignleft actions">';
			submit_button( __( 'Call a courier', 'woocommerce-edostavka' ), '', 'call-courier-action', false );
			echo '</div>';
		}
	}

	public function get_orders_to_call_courier() {
		/** @var WC_Order_Data_Store_CPT $data_store */
		$data_store = WC_Data_Store::load( 'order' );
		$orders     = $data_store->query( array(
			'status'        => array( 'wc-pending', 'wc-processing', 'wc-on-hold' ),
			'type'          => wc_get_order_types( 'order-count' ),
			'limit'         => -1,
			'is_edostavka'  => true,
			'is_courier'    => true
		) );

		$results = array();

		foreach ( ( array ) $orders as $order_post ) {
			$order = new WC_Edostavka_Shipping_Order( $order_post );
			$results[ $order->get_id() ] = sprintf( __( 'Order #%s [%s] [%s at %s]', 'woocommerce-edostavka' ), $order->get_order_number(), $order->get_formatted_billing_full_name(), date_i18n( wc_date_format(), strtotime( $order->get_date_created() ) ), date_i18n( wc_time_format(), strtotime( $order->get_date_created() ) ) );
		}

		return $results;
	}

	public function prepare_items() {

		$per_page = $this->get_items_per_page( 'wc_edostavka_orders_edit_per_page' );

		// main query args
		$args = array(
			'paginate'      => true,
			'status'        => array_keys( wc_get_order_statuses() ),
			'type'          => wc_get_order_types( 'view-orders' ),
			'limit'         => $per_page,
			'paged'         => $this->get_pagenum(),
			'orderby'       => $this->get_current_orderby(),
			'order'         => $this->get_current_order(),
			'is_edostavka'  => true
		);

		if( isset( $_GET['edostavka_order_status'] ) && 'all' !== wc_clean( $_GET['edostavka_order_status'] ) ) {
			$args['edostavka_status'] = wc_clean( $_GET['edostavka_order_status'] );
		}

		// handle search
		$args = $this->add_search_args( $args );

		try {

			/** @var WC_Order_Data_Store_CPT $data_store */
			$data_store = WC_Data_Store::load( 'order' );
			$orders     = $data_store->query( $args );

			foreach ( ( array ) $orders->orders as $order_post ) {
				$order = new WC_Edostavka_Shipping_Order( $order_post );
				$this->items[] = $order;
			}

			$this->set_pagination_args(
				array(
					'total_items' => $orders->total,
					'per_page'    => $per_page,
					//'total_pages' => ceil( $orders->total / $per_page ),
					'total_pages' => $orders->max_num_pages
				)
			);

		} catch ( Exception $e ) {
			wc_edostavka_shipping()->log( $e->getMessage(), 'edostavka_exception_error' );
		}
	}

	public function no_items() {
		esc_html_e( 'No CDEK orders found.', 'woocommerce-edostavka' );
	}

	/**
	 * Adds in any query arguments based on the search term
	 *
	 * @param array $args associative array of WP_Query arguments used to query and populate the list table
	 * @return array associative array of WP_Query arguments used to query and populate the list table
	 */
	private function add_search_args( $args ) {

		global $wpdb;

		if ( isset( $_GET['s'] ) && $_GET['s'] ) {

			$search_fields = array_map( 'esc_attr', array(
				'_order_key',
				'_billing_email',
				'_wc_edostavka_status',
			) );

			$search_order_id = str_replace( 'Order #', '', $_GET['s'] );
			if ( ! is_numeric( $search_order_id ) ) {
				$search_order_id = 0;
			}

			// Search orders
			$post_ids = array_merge(
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT post_id
						FROM {$wpdb->postmeta}
						WHERE meta_key IN ('" . implode( "','", $search_fields ) . "')
						AND meta_value LIKE '%%%s%%'",
						esc_attr( $_GET['s'] )
					)
				),
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT order_id
						FROM {$wpdb->prefix}woocommerce_order_items as order_items
						WHERE order_item_name LIKE '%%%s%%'
						",
						esc_attr( $_GET['s'] )
					)
				),
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT posts.ID
						FROM {$wpdb->posts} as posts
						LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
						LEFT JOIN {$wpdb->users} as users ON postmeta.meta_value = users.ID
						WHERE
							post_excerpt LIKE '%%%1\$s%%' OR
							post_title   LIKE '%%%1\$s%%' OR
							(
								meta_key = '_customer_user' AND
								(
									user_login    LIKE '%%%1\$s%%' OR
									user_nicename LIKE '%%%1\$s%%' OR
									user_email    LIKE '%%%1\$s%%' OR
									display_name  LIKE '%%%1\$s%%'
								)
							)
						",
						esc_attr( $_GET['s'] )
					)
				),
				array( $search_order_id )
			);

			$args['post__in'] = $post_ids;
		}

		return $args;
	}
}
