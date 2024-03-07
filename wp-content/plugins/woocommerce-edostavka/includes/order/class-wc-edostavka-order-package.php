<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Order_Package implements WC_Edostavka_Order_Package_Interface {
	/**
	 * @var string|integer
	 */
	private $key;
	/**
	 * @var string
	 */
	private $number;

	/**
	 * @var int
	 */
	private $weight;

	/**
	 * @var int
	 */
	private $length;

	/**
	 * @var int
	 */
	private $width;

	/**
	 * @var int
	 */
	private $height;

	/**
	 * @var WC_Edostavka_Order_Item[]
	 */
	private $items;

	/**
	 * @param string|integer $key Unique key of package
	 * @param string $number The package number
	 * @param integer $weight Total weight (in grams)
	 * @param integer $length Package dimensions. Length (in centimeters)
	 * @param integer $width Package dimensions. Width (in centimeters)
	 * @param integer $height Package dimensions. Height (in centimeters)
	 * @param WC_Edostavka_Order_Item[] $items Items in the package
	 */
	public function __construct( $key, $number, $weight, $length, $width, $height, $items = array() ) {
		$this->key      = $key;
		$this->number   = $number;
		$this->weight   = $weight;
		$this->length   = $length;
		$this->width    = $width;
		$this->height   = $height;
		$this->items    = $items;
	}

	/**
	 * @inheritDoc
	 */
	public function get_unique_key() {
		return $this->key;
	}
	/**
	 * @inheritDoc
	 */
	public function get_number() {
		return $this->number;
	}

	/**
	 * @inheritDoc
	 */
	public function get_weight() {
		return $this->weight;
	}

	/**
	 * @inheritDoc
	 */
	public function get_length() {
		return $this->length;
	}

	/**
	 * @inheritDoc
	 */
	public function get_width() {
		return $this->width;
	}

	/**
	 * @inheritDoc
	 */
	public function get_height() {
		return $this->height;
	}

	/**
	 * @inheritDoc
	 */
	public function get_items() {
		return $this->items;
	}
}
