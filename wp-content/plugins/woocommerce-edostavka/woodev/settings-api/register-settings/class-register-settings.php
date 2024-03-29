<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Woodev_Register_Settings' ) ) :

	/**
	 * Base settings class - Shared between SP Lite & Pro (and other plugins)
	 */
	abstract class Woodev_Register_Settings {

		protected $settings = array();
		protected $option;

		/**
		 * Class constructor.
		 */
		public function __construct( $option ) {
			$this->option = $option;

			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Register the settings.
		 */
		public function register_settings() {
			register_setting( $this->option, $this->option );
		}

		/**
		 * Function to set default options on a fresh install.
		 */
		public function set_defaults( $settings = array() ) {

			if ( false === get_option( $this->option ) ) {

				$this->settings = $settings;

				update_option( $this->option, $this->settings );
			}
		}

		/**
		 * Loads the specified template file.
		 */
		public function load_template( $file ) {
			include_once( $file );
		}

		/**
		 * Add a specific setting with a specified value.
		 */
		public function add_setting( $setting, $value ) {
			$settings             = get_option( $this->option );
			$settings[ $setting ] = $value;

			$this->update_settings( $settings );
		}

		/**
		 * Return all the settings.
		 */
		public function get_settings() {
			$saved_settings = is_array( get_option( $this->option ) ) ? get_option( $this->option ) : array();

			return array_merge( $this->settings, $saved_settings );
		}

		/**
		 * Delete a setting.
		 */
		public function delete_setting( $setting ) {
			$settings = get_option( $this->option );

			if ( isset( $settings[ $setting ] ) ) {
				unset( $settings[ $setting ] );
			}

			// Since the class method update_settings merges the arrays together we need to update manually here to fully rid of the deleted setting.
			$this->settings = $settings;
			update_option( $this->option, $this->settings );
		}

		/**
		 * Updates the settings in the database.
		 */
		public function update_settings( $settings = array() ) {

			$old_settings = get_option( $this->option );

			if ( false === $old_settings ) {
				$old_settings = $this->settings;
			}

			$this->settings = array_merge( $old_settings, $settings );

			foreach ( $this->settings as $setting ) {
				if ( empty( $setting ) ) {
					unset( $this->settings[ $setting ] );
				}
			}

			update_option( $this->option, $this->settings );
		}

		/**
		 * Print out the settings to the screen. Mostly used for debugging.
		 */
		public function print_settings() {
			$settings = get_option( $this->option );

			echo '<pre>' . print_r( $settings, true ) . '</pre>';
		}

		/**
		 * Return a specific setting.
		 * Will return the setting if successful or will return null if not successful.
		 */
		public function get_setting_value( $id ) {

			$settings = is_array( get_option( $this->option ) ) ? get_option( $this->option ) : array();

			$this->settings = $settings;

			// Only return it if it is set and it is not empty.
			if ( isset( $settings[ $id ] ) && ! empty( $settings[ $id ] ) ) {
				return $settings[ $id ];
			}

			return null;
		}

		/**
		 * Create an ID for the specified $id.
		 */
		public function get_setting_id( $id ) {
			return $this->option . '[' . $id . ']';
		}

		/**
		 * Returns this class' option value.
		 */
		public function get_option() {
			return $this->option;
		}

		/**
		 * Delete the option out of the database.
		 */
		public function delete_option() {
			delete_option( $this->option );
		}
	}

endif;