<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Order_Item implements WC_Edostavka_Order_Item_Interface {

	private $name;

	private $war_key;

	private $value;

	private $cost;

	private $weight;

	private $weight_gross;

	private $amount;

	/**
	 * @param string $name Product name (may also contain product description: size, color)
	 * @param string $key Identifier/item number of the goods
	 * @param float $value Cash on delivery amount
	 * @param float $cost Declared value of the goods
	 * @param integer $weight Weight (per unit, in grams)
	 * @param integer|null $weight_gross Gross weight
	 * @param integer $amount Number of units
	 */
	public function __construct( $name, $key, $value, $cost, $weight, $weight_gross = null, $amount = 1 ) {
		$this->name         = $name;
		$this->war_key      = $key;
		$this->value        = $value;
		$this->cost         = $cost;
		$this->weight       = $weight;
		$this->weight_gross = $weight_gross && $weight_gross >= $weight ? $weight_gross : $weight;
		$this->amount       = $amount;
	}

	/**
	 * @param integer $amount
	 *
	 * @return void
	 */
	public function set_amount( $amount = 0 ) {
		$this->amount = $amount;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	public function get_ware_key() {
		return $this->war_key;
	}

	/**
	 * @inheritDoc
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @inheritDoc
	 */
	public function get_cost() {
		return $this->cost;
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
	public function get_weight_gross() {
		return $this->weight_gross;
	}

	/**
	 * @inheritDoc
	 */
	public function get_amount() {
		return $this->amount;
	}
}
