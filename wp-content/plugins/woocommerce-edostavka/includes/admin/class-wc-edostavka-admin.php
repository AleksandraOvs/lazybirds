<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_Admin {

	/** @var Woodev_Admin_Message_Handler instance */
	public $message_handler;

	/**
	 * The  cdek orders list table object.
	 *
	 * @var WC_Edostavka_Order_List_Table
	 */
	private $orders_list_table;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'includes' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ), 10, 2 );

		//Temporary disabled because it not usefully
		//add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_order_actions' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'handle_actions' ) );
		add_action( 'admin_init', array( $this, 'download_export_file' ) );

		add_action( 'in_admin_header',   array( $this, 'load_orders_list_table' ) );
		add_filter( 'set-screen-option', array( $this, 'set_orders_list_option' ), 10, 3 );

		add_action( 'before_delete_post', array( $this, 'before_delete_order' ) );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'set_cancelled_status' ) );

		add_action( 'admin_footer', array( $this, 'load_templates' ) );

		add_filter( 'woocommerce_email_setting_columns', array( $this, 'add_preview_email_column' ) );
		add_action( 'woocommerce_email_setting_column_preview', array( $this, 'add_preview_email_action' ) );
		add_action( 'admin_init', array( $this, 'preview_emails' ) );
	}

	public function includes() {
		if ( apply_filters( 'wc_edostavka_enable_admin_help_tab', true ) ) {
			include_once( wc_edostavka_shipping()->get_plugin_path() . '/includes/admin/class-wc-edostavka-admin-help.php' );
		}
	}

	protected function export_allowed() {
		return current_user_can( 'manage_woocommerce' ) && current_user_can( 'export' );
	}

	public function add_menu_link() {

		$order_count = wc_edostavka_get_counts_order_status();
		$new_orders = 0;

		foreach( $order_count as $row ) {
			if( $row->status !== 'NEW' ) continue;
			$new_orders = $row->count;
		}

		$menu_name = __( 'CDEK Orders', 'woocommerce-edostavka' );

		if( $new_orders > 0 ) {
			$menu_name .= sprintf( ' <span class="update-plugins count-%d"><span class="new-count">%d</span></span>', $new_orders, number_format_i18n( $new_orders ) );
		}

		$hook = add_submenu_page(
			'woocommerce',
			__( 'CDEK Orders', 'woocommerce-edostavka' ),
			$menu_name,
			'manage_woocommerce',
			'wc_edostavka_orders',
			array( $this, 'show_sub_menu_page' )
		);

		add_action( 'load-woocommerce_page_wc_edostavka_orders', array( $this, 'add_options' ) );
		add_action( 'load-' . $hook, array( $this, 'process_actions' ) );
		add_action( 'admin_print_scripts-' . $hook, array( $this, 'load_scripts' ) );
	}

	public function load_scripts() {
		wp_enqueue_style( 'wc-edostavka-orders-table', wc_edostavka_shipping()->get_plugin_url() . '/assets/css/admin/orders-table.css', array( 'woocommerce_admin_styles' ), WC_CDEK_SHIPPING_VERSION );
		wp_enqueue_script( 'wc-edostavka-orders-table', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/admin/orders-table.js', array( 'jquery', 'wc-backbone-modal', 'wc-enhanced-select', 'jquery-blockui' ), WC_CDEK_SHIPPING_VERSION, true );
		wp_localize_script( 'wc-edostavka-orders-table', 'wc_edostavka_orders_params', array(
			'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
			'orders_nonce'  => wp_create_nonce( 'wc-edostavka-orders-table' ),
			'preview_nonce' => wp_create_nonce( 'wc-edostavka-preview-order' )
		) );
	}

	/**
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 *
	 * @return void
	 */
	public function register_metabox( $post_type, $post ) {

		if( in_array( $post_type, wc_get_order_types(), true ) ) {

			$order = new WC_Edostavka_Shipping_Order( $post->ID );

			if( $order->has_edostavka_shipping() ) {

				add_meta_box(
					'wc_edostavka_order_meta_box',
					__( 'CDEK Order information', 'woocommerce-edostavka' ),
					array( $this, 'show_order_meta_box' ),
					$post_type,
					'side'
				);
			}
		}
	}

	public function show_order_meta_box( $post ) {
		$order = new WC_Edostavka_Shipping_Order( $post->ID );

		$this->message_handler->show_messages();

		wc_edostavka_shipping()->load_template( 'views/html-order-meta-box.php', array(
			'order'     => $order,
			'actions'   => wc_render_action_buttons( $this->get_orders_list_table()->get_order_actions( $order ) )
		) );
	}

	public function add_order_actions( $actions, $order ) {

		$order = new WC_Edostavka_Shipping_Order( $order );

		if( $order->has_edostavka_shipping() ) {

			$actions = array_merge( $actions, $this->get_orders_list_table()->get_order_actions( $order ) );
		}

		return $actions;
	}

	public function show_sub_menu_page() {

		echo '<div class="wrap woocommerce wc-edostavka-orders-wrapper">';
		$this->show_manage_tab();
		echo '</div>';
	}

	public function process_manage_tab() {

		$action = $this->get_orders_list_table()->current_action();
		$bulk_actions = $this->get_orders_list_table()->get_bulk_actions();

		if( $action && in_array( $action, array_keys( $bulk_actions ), true ) ) {

			if ( isset( $_GET['page'] ) && 'wc_edostavka_orders' == $_GET['page'] ) {

				$order_ids = isset( $_REQUEST['order_id'] ) ? array_map( 'absint', $_REQUEST['order_id'] ): array();

				if ( empty( $order_ids ) ) {
					if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
						$this->message_handler->add_info( sprintf( __( 'Action "%s" have not been completed, because you have not selected any order.', 'woocommerce-edostavka' ), $bulk_actions[ $action ] ) );
						wp_redirect( esc_url_raw( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'action2' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) ) );
						exit;
					}
					return;
				}

				$messages = array(
					'success'   => array(),
					'error'     => array()
				);

				foreach ( $order_ids as $order_id ) {

					$order = new WC_Edostavka_Shipping_Order( $order_id );

					switch ( $action ) {
						case 'export' :
							if( ! $order->is_exported() ) {
								if ( $order->export_action() ) {
									$messages['success'][] = sprintf( __( 'Order %s was successfully exported to CDEK', 'woocommerce-edostavka' ), $order->get_order_number() );
								} else {
									$messages['error'][] = sprintf( __( 'Order %s could not be exported.', 'woocommerce-edostavka' ), $order->get_order_number() );
								}
							}
							break;
						case 'update' :
							if( $order->is_exported() ) {
								if( $order->update_order() ) {
									$messages['success'][] = sprintf( __( 'Order %s was successfully updated.', 'woocommerce-edostavka' ), $order->get_order_number() );
								} else {
									$messages['error'][] = sprintf( __( 'Order %s could not be updated.', 'woocommerce-edostavka' ), $order->get_order_number() );
								}
							}
							break;
						case 'cancel' :
							if( $order->is_exported() ) {
								if( $order->cancel_action() ) {
									$messages['success'][] = sprintf( __( 'Order %s was successfully canceled.', 'woocommerce-edostavka' ), $order->get_order_number() );
								} else {
									$messages['error'][] = sprintf( __( 'Order %s could not be canceled.', 'woocommerce-edostavka' ), $order->get_order_number() );
								}
							}
							break;
					}
				}

				foreach( $messages as $type => $message_items ) {

					if( ! empty( $message_items ) ) {

						foreach ( $message_items as $message ) {

							switch ( $type ) {
								case 'success' :
									$this->message_handler->add_message( $message );
									break;
								case 'error' :
									$this->message_handler->add_error( $message );
									break;
							}
						}

					}
				}

				$this->redirect_with_notice();
			}
		}
	}

	public function process_actions() {
		$this->process_manage_tab();
	}

	public function load_orders_list_table() {
		if ( isset( $_GET['page'] ) && 'wc_edostavka_orders' == $_GET['page'] ) {
			$this->get_orders_list_table();
		}
	}

	private function get_orders_list_table() {

		if ( ! isset( $this->orders_list_table ) ) {
			$this->orders_list_table = new WC_Edostavka_Order_List_Table();
		}

		return $this->orders_list_table;
	}

	private function show_manage_tab() {
		$manage_table = $this->get_orders_list_table();
		$manage_table->prepare_items();

		echo '<form method="get" id="mainform" action="" enctype="multipart/form-data">';
		echo '<h2>' . __( 'Manage CDEK Orders', 'woocommerce-edostavka' );
		if ( isset( $_GET['s'] ) && $_GET['s'] ) {
			echo '<span class="subtitle">' . sprintf( __( 'Search results for "%s"', 'woocommerce-edostavka' ), $_GET['s'] ) . '</span>';
		}
		echo '</h2>';

		$this->message_handler->show_messages();

		// Display the views
		$manage_table->views();
		$manage_table->search_box( __( 'Search Orders', 'woocommerce-edostavka' ), 'edostavka_order' );

		if ( ! empty( $_REQUEST['edostavka_status'] ) ) {
			echo '<input type="hidden" name="edostavka_status" value="' . esc_attr( $_REQUEST['edostavka_status'] ) . '" />';
		}

		echo '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />';

		// display the list table
		$manage_table->display();
		echo '</form>';
	}

	public function add_options() {
		$args = array(
			'label'   => __( 'CDEK Orders per page', 'woocommerce-edostavka' ),
			'default' => 20,
			'option'  => 'wc_edostavka_orders_edit_per_page',
		);

		add_screen_option( 'per_page', $args );
	}

	public function set_orders_list_option( $status, $option, $value ) {
		if ( 'wc_edostavka_orders_edit_per_page' == $option ) {
			return $value;
		}

		return $status;
	}

	public function before_delete_order( $order_id ) {
		if( in_array( get_post_type( $order_id ), wc_get_order_types(), true ) ) {
			$order = new WC_Edostavka_Shipping_Order( $order_id );
			if( $order->has_edostavka_shipping() && $order->is_exported() ) {
				$order->cancel_action();
			}
		}
	}

	public function set_cancelled_status( $order_id ) {

		$order = new WC_Edostavka_Shipping_Order( $order_id );

		if( $order->has_edostavka_shipping() ) {

			if( in_array( wc_strtoupper( $order->get_order_status() ), array( 'NEW', 'ACCEPTED', 'CREATED', 'INVALID' ), true ) ) {

				if( ! $order->is_exported() ) {
					$order->set_order_status( 'CANCELED' );
					$order->add_order_note( sprintf( __( 'The order #%s was cancel by manager.', 'woocommerce-edostavka' ), $order->get_id() ) );
				} else {
					$order->cancel_action();
				}
			} else {
				throw new Exception( sprintf( __( 'Cannot cancel order #%s because it is not editable.', 'woocommerce-edostavka' ), $order->get_order_number() ) );
			}
		}
	}

	public function load_templates() {

		$courier_orders = $this->get_orders_list_table()->get_orders_to_call_courier();

		if( count( $courier_orders ) > 0 ) {
			wc_edostavka_shipping()->load_template( 'views/html-call-courier-modal.php', array(
				'orders'    => $courier_orders
			) );
		}

		wc_edostavka_shipping()->load_template( 'views/html-order-preview-modal.php' );
	}

	public function handle_actions() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$current_action = ( empty( $_REQUEST['action'] ) )  ? null : sanitize_text_field( urldecode( $_REQUEST['action'] ) );

		if ( wc_edostavka_shipping()->is_plugin_settings() && $current_action ) {

			switch ( $current_action ) {
				case 'update_orders' :
					$updated_count = wc_edostavka_update_all_orders();
					$message = sprintf( _n( 'Updated information for %d order.', 'Updated information for %d orders.', $updated_count, 'woocommerce-edostavka' ), $updated_count );
					break;
				default :
					$message = __( 'Nothing to do.', 'woocommerce-edostavka' );
					break;
			}

			$this->message_handler->add_message( $message );

			$this->redirect_with_notice();
		}
	}

	/**
	 * @throws Exception
	 */
	public function download_export_file() {

		if ( isset( $_GET['action'], $_GET['nonce'], $_GET['order_id'], $_GET['file_type'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'order-file-export' ) && 'download_order_file' === wp_unslash( $_GET['action'] ) ) {

			try {

				if ( ! $this->export_allowed() ) {
					throw new Woodev_Plugin_Exception( __( 'Insufficient privileges to export file.', 'woocommerce-edostavka' ) );
				}

				$order_id = absint( wp_unslash( $_GET['order_id'] ) );
				$file_type = wp_unslash( $_GET['file_type'] );

				if( ! in_array( $file_type, array( 'waybill', 'barcode' ), true ) ) {
					throw new Woodev_Plugin_Exception( __( 'Incorrect file type request.', 'woocommerce-edostavka' ) );
				}

				$order = new WC_Edostavka_Shipping_Order( $order_id );

				$cache_key   = WC_Cache_Helper::get_cache_prefix( 'orders' ) . "_cdek_order_{$file_type}_id_" . $order->get_id();
				$cached_file_id = wp_cache_get( $cache_key );

				$api = wc_edostavka_shipping()->get_api();

				if( false === $cached_file_id ) {

					$request = array( 'order_uuid'   => $order->get_order_meta( 'cdek_order_id' ) );

					switch ( $file_type ) {
						case 'waybill' :
							$response = $api->create_order_waybill( $request );
							break;
						case 'barcode':
							$response = $api->create_order_barcode( $request );
							break;
						default :
							throw new Woodev_Plugin_Exception( __( 'Incorrect file type request.', 'woocommerce-edostavka' ) );
					}


					if( $entity_id = $response->get_entity_uuid() ) {
						$cached_file_id = $entity_id;
						wp_cache_set( $cache_key, $cached_file_id );
						sleep( 1 );
					}
				}

				$response = $api->get_print_file( $cached_file_id, $file_type );
				$file_content = $response->to_string();

				if( ! class_exists( 'WC_Edostavka_File_Exporter' ) ) {
					require_once( wc_edostavka_shipping()->get_plugin_path() . '/includes/admin/class-wc-edostavka-file-exporter.php' );
				}

				$exporter = new WC_Edostavka_File_Exporter( $file_content, array(
					'file_name' => sprintf( 'wc_edostavka_%s_%s_%s', $file_type, $order->get_order_number(), $cached_file_id )
				) );

				$order->update_order_meta( "{$file_type}_downloaded", microtime() );

				$exporter->generate_file();
				$exporter->export();

			} catch ( Woodev_Plugin_Exception $e ) {
				//throw new Woodev_Plugin_Exception( $e->getMessage() );
				wc_edostavka_shipping()->log( $e->getMessage() );
			}
		}
	}

	protected function redirect_with_notice() {

		if ( isset( $_REQUEST['_wp_http_referer'] ) ) {
			$redirect_url = $_REQUEST['_wp_http_referer'];
		} else {
			$redirect_url = remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'action2', 'order_id' ), stripslashes( $_SERVER['REQUEST_URI'] ) );
		}

		wp_safe_redirect( esc_url_raw( $this->message_handler->redirect( $redirect_url, null ) ) );
		exit;
	}

	public function add_preview_email_column( $columns ) {

		if( ! isset( $columns['preview'] ) ) {
			$columns['preview'] = '';
		}

		return $columns;
	}

	/**
	 * @param WC_Email $email
	 */
	public function add_preview_email_action( $email ) {

		echo '<td class="wc-email-settings-table-preview">';

		if( is_a( $email, 'WC_Edostavka_Email' ) && $email->has_preview() ) {
			printf( '<a class="button alignright" href="%s" target="_blank">%s</a>', wp_nonce_url( add_query_arg( array( 'preview_edostavka_mail' => true, 'email_id' => $email->id ), admin_url() ), 'edostavka-mail' ), __( 'Preview', 'woocommerce-edostavka' ) );
		}

		echo '</td>';
	}

	public function preview_emails() {
		if ( isset( $_GET['preview_edostavka_mail'] ) ) {
			if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'edostavka-mail' ) ) ) {
				die( 'Security check' );
			}

			$email_id   = wc_clean( $_REQUEST['email_id'] );
			$mailer     = WC()->mailer();
			$email      = null;

			foreach ( $mailer->get_emails() as $email_class ) {
				if( $email_class->id !== $email_id ) continue;
				$email = $email_class;
			}

			if( $email && is_a( $email, 'WC_Edostavka_Email' ) && $email->has_preview() ) {
				$email->preview_emails();
			}

			exit;
		}
	}
}
