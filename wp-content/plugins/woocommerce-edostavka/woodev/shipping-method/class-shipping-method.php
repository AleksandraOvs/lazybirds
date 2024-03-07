<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Woodev_Shipping_Method' ) ) :

abstract class Woodev_Shipping_Method extends WC_Shipping_Method {

	/** The production environment identifier */
	const ENVIRONMENT_PRODUCTION = 'production';

	/** The test environment identifier */
	const ENVIRONMENT_TEST = 'test';

	/** Debug mode log to file */
	const DEBUG_MODE_LOG = 'log';

	/** Debug mode display on checkout */
	const DEBUG_MODE_CHECKOUT = 'checkout';

	/** Debug mode log to file and display on checkout */
	const DEBUG_MODE_BOTH = 'both';

	/** Debug mode disabled */
	const DEBUG_MODE_OFF = 'off';
	
	const FEATURE_SHIPPING_ZONES = 'shipping-zones';
	
	const FEATURE_INSTANCE_SETTINGS = 'instance-settings';
	
	const FEATURE_INSTANCE_SETTINGS_MODAL = 'instance-settings-modal';
	
	const FEATURE_SETTINGS = 'settings';

	/** @var Woodev_Shipping_Plugin the parent plugin class */
	private $plugin;

	/** @var array associative array of environment id to display name, defaults to 'production' => 'Production' */
	private $environments;

	/** @var array optional array of currency codes this method is allowed for */
	protected $currencies;

	/** @var string configuration option: the transaction environment, one of $this->environments keys */
	private $environment;

	/** @var string configuration option: 4 options for debug mode - off, checkout, log, both */
	private $debug_mode;
	
	public function __construct( $instance_id  = 0, $args = array() ) {

		parent::__construct( $instance_id );

		$this->plugin = $plugin;

		$this->get_plugin()->set_method( $id, $this );

		// optional parameters
		if ( isset( $args['method_title'] ) ) {
			$this->method_title = $args['method_title'];
		}

		if ( isset( $args['method_description'] ) ) {
			$this->method_description = $args['method_description'];
		}

		if ( isset( $args['supports'] ) ) {
			$this->set_supports( $args['supports'] );
		}

		if ( isset( $args['environments'] ) ) {
			$this->environments = array_merge( $this->get_environments(), $args['environments'] );
		}

		if ( isset( $args['countries'] ) ) {
			$this->countries = $args['countries'];
		}

		if ( isset( $args['currencies'] ) ) {
			$this->currencies = $args['currencies'];
		} else {
			$this->currencies = $this->get_plugin()->get_accepted_currencies();
		}
	}
	
}

endif;

?>