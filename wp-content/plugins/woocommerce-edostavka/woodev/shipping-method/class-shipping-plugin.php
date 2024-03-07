<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Woodev_Shipping_Plugin' ) ) :

	abstract class Woodev_Shipping_Plugin extends Woodev_Plugin {

		/** @var array optional associative array of shipping id to array( 'class_name' => string, 'method' => Woodev_Shipping_Method ) */
		private $methods;

		/** @var array optional array of currency codes this shipping method is allowed for */
		private $currencies = array();

		/** @var array named features that this method supports which require action from the parent plugin */
		private $supports = array();

		/**
		 * Initializes the plugin.
		 *
		 * @param string $id plugin id
		 * @param string $version plugin version number
		 * @param array $args plugin arguments
		 *
		 * @see Woodev_Plugin::__construct()
		 * @since 1.2.0
		 */
		public function __construct( $id, $version, $args ) {

			parent::__construct( $id, $version, $args );

			$args = wp_parse_args( $args, array(
				'methods'    => array(),
				'supports'    => array(),
				'currencies' => array()
			) );

			// add each shipping methods
			foreach ( $args['methods'] as $method_id => $method_class_name ) {
				$this->add_method( $method_id, $method_class_name );
			}

			$this->currencies = (array) $args['currencies'];
			$this->supports    = (array) $args['supports'];

			// require the files
			$this->includes();

			// add the action & filter hooks
			$this->add_hooks();
		}

		/**
		 * Adds the action & filter hooks.
		 */
		private function add_hooks() {

			// add classes to WC Shipping Methods
			add_action( 'woocommerce_shipping_init', array( $this, 'shipping_init' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'add_shipping_methods' ) );

			// add shipping method information to the system status report
			add_action( 'woocommerce_system_status_report', array( $this, 'add_system_status_information' ) );
		}

		/**
		 * Include required files.
		 *
		 * @internal
		 *
		 * @since 1.2.0
		 */
		private function includes() {
			$framework_path = $this->get_shipping_method_framework_path();
		}

		public function shipping_init() {

		}

		public function add_shipping_methods() {

		}

		/**
		 * Adds the given method id and method class name as an available shipping method supported by this plugin
		 *
		 * @param string $method_id the method identifier
		 * @param string $method_class_name the corresponding shipping method class name
		 *
		 * @since 1.2.0
		 *
		 */
		public function add_method( $method_id, $method_class_name ) {
			$this->methods[ $method_id ] = array( 'class_name' => $method_class_name, 'method' => null );
		}

		/**
		 * Gets all supported method class names
		 *
		 * @return array of string method class names
		 * @since 1.2.0
		 *
		 */
		public function get_method_class_names() {

			assert( ! empty( $this->methods ) );

			$class_names = array();

			foreach ( $this->methods as $method ) {
				$class_names[] = $method['class_name'];
			}

			return $class_names;
		}


		/**
		 * Gets the method class name for the given method id
		 *
		 * @param string $method_id the method identifier
		 *
		 * @return string method class name
		 * @since 1.2.0
		 *
		 */
		public function get_method_class_name( $method_id ) {

			assert( isset( $this->methods[ $method_id ]['class_name'] ) );

			return $this->methods[ $method_id ]['class_name'];
		}

		/**
		 * Gets all supported method objects
		 *
		 * @return Woodev_Shipping_Method[]
		 * @since 1.2.0
		 *
		 */
		public function get_methods() {

			assert( ! empty( $this->methods ) );

			$methods = array();

			foreach ( $this->get_method_ids() as $method_id ) {
				$methods[] = $this->get_method( $method_id );
			}

			return $methods;
		}


		/**
		 * Adds the given $method to the internal methods store
		 *
		 * @param string                 $method_id the method identifier
		 * @param Woodev_Shipping_Method $method    the method object
		 * @param int                    $instance_id the method instance ID if it supported
		 */
		public function set_method( $method_id, $method, $instance_id = 0 ) {
			$this->methods[ $method_id ]['gateway'] = $method;
		}

		/**
		 * Returns the identified method object
		 *
		 * @param string $method_id optional method identifier, defaults to first method
		 *
		 * @return Woodev_Shipping_Method the method object
		 * @since 1.2.0
		 *
		 */
		public function get_method( $method_id = null ) {

			// default to first method
			if ( is_null( $method_id ) ) {
				reset( $this->methods );
				$method_id = key( $this->methods );
			}

			if ( ! isset( $this->methods[ $method_id ]['method'] ) ) {

				// instantiate and cache
				$class_name = $this->get_method_class_name( $method_id );
				$this->set_method( $method_id, new $class_name() );
			}

			return $this->methods[ $method_id ]['method'];
		}


		/**
		 * Returns true if the plugin supports this method
		 *
		 * @param string $method_id the method identifier
		 *
		 * @return boolean true if the plugin has this method available, false otherwise
		 * @since 1.2.0
		 *
		 */
		public function has_method( $method_id ) {
			return isset( $this->methods[ $method_id ] );
		}

		/**
		 * Returns all available method ids for the plugin
		 *
		 * @return array of method id strings
		 * @since 1.0.0
		 *
		 */
		public function get_method_ids() {

			assert( ! empty( $this->methods ) );

			return array_keys( $this->methods );
		}


		/**
		 * Returns the set of accepted currencies, or empty array if all currencies are accepted.
		 * This is the intersection of all currencies accepted by any methods this plugin supports.
		 *
		 * @since 1.2.0
		 *
		 *@return array of accepted currencies
		 */
		public function get_accepted_currencies() {
			return $this->currencies;
		}

		/**
		 * Returns the loaded shipping method framework __FILE__
		 *
		 * @since 1.2.0
		 *
		 * @return string
		 */
		public function get_shipping_method_framework_file() {
			return __FILE__;
		}


		/**
		 * Returns the loaded shipping method framework path, without trailing slash.
		 *
		 * This is the highest version shipping method framework that was loaded by the bootstrap.
		 *
		 * @since 1.2.0
		 *
		 * @return string
		 */
		public function get_shipping_method_framework_path() {
			return untrailingslashit( plugin_dir_path( $this->get_shipping_method_framework_file() ) );
		}


		/**
		 * Returns the absolute path to the loaded shipping method framework image directory, without a trailing slash
		 *
		 * @since 1.2.0
		 *
		 * @return string relative path to framework image directory
		 */
		public function get_shipping_method_framework_assets_path() {
			return $this->get_shipping_method_framework_path() . '/assets';
		}


		/**
		 * Returns the loaded payment gateway framework assets URL, without a trailing slash
		 *
		 * @since 1.2.0
		 *
		 * @return string
		 */
		public function get_shipping_method_framework_assets_url() {
			return untrailingslashit( plugins_url( '/assets', $this->get_shipping_method_framework_file() ) );
		}
	}

endif;