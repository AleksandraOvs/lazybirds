<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Edostavka Cron Class
 *
 * Adds custom update schedule and schedules order update events
 *
 * @since 2.2.0
 */
class WC_Edostavka_Cron {

	private $orders_updates_enabled = false;

	/**
	 * @var int
	 */
	private $orders_update_interval;

	public function __construct() {

		add_filter( 'cron_schedules', array( $this, 'add_update_schedules' ) );
		add_action( 'init', array( $this, 'add_scheduled_updates' ) );

		add_action( 'wc_edostavka_orders_update', 'wc_edostavka_update_all_orders' );

		$settings = get_option( 'woocommerce_edostavka_settings', array() );

		$this->orders_updates_enabled = isset( $settings[ 'cron_auto_update_orders' ] ) && wc_string_to_bool( $settings[ 'cron_auto_update_orders' ] );
		$this->orders_update_interval = $this->orders_updates_enabled ? $settings['cron_auto_update_orders_interval'] : 0;

	}

	/**
	 * If updates are enabled, add the custom interval (e.g. every 15 minutes) set on the admin settings page
	 *
	 * @since 2.2.0
	 * @param array $schedules existing WP recurring schedules
	 * @return array
	 */
	public function add_update_schedules( $schedules ) {

		if ( $this->orders_update_interval ) {
			$schedules['wc_edostavka_orders' ] = array(
				'interval' => $this->orders_update_interval * MINUTE_IN_SECONDS,
				'display'  => sprintf( __( 'Every %s minutes', 'woocommerce-edostavka' ), $this->orders_update_interval )
			);
		}

		return $schedules;
	}

	/**
	 * Add scheduled events to wp-cron if not already added
	 *
	 * @return void
	 * @since 2.2.0
	 */
	public function add_scheduled_updates() {
		if ( $this->orders_updates_enabled && ! wp_next_scheduled( 'wc_edostavka_orders_update' ) ) {
			wp_schedule_event( time() + ( intval( $this->orders_update_interval ) * MINUTE_IN_SECONDS ), 'wc_edostavka_orders', 'wc_edostavka_orders_update' );
		}
	}
}
