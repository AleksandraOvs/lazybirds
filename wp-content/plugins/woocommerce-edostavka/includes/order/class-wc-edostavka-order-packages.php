<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Order_Packages {

	/**
	 * @var WC_Order_Item_Product[]
	 */
	private $items = array();

	/**
	 * @var WC_Order
	 */
	private $order;

	/**
	 * @var bool|WC_Shipping_Method
	 */
	private $shipping_method;

	/**
	 * @var WC_Edostavka_Box_Packer
	 */
	private $packer;
	/**
	 * @var WC_Edostavka_Order_Package[]
	 */
	private $packages = array();


	public function __construct( WC_Edostavka_Order_Factory $order ) {

		$this->items           = $order->get_items();
		$this->order           = $order->get_order();
		$this->shipping_method = $order->get_shipping_method();
		$this->packer          = wc_edostavka_get_package_box_handler( $order->get_packing_method() );

		$this->create_packages();
	}

	public function get_packages() {

		$packages = array();

		$names = array();

		foreach ( $this->packages as $index => $package ) {

			$items = array();

			/** @var WC_Edostavka_Order_Item $item */
			foreach ( $package->get_items() as $item ) {
				$items[] = apply_filters( 'wc_edostavka_order_package_item', array(
					'name'         => $item->get_name(),
					'ware_key'     => $item->get_ware_key(),
					'payment'      => array(
						'value' => $item->get_value()
					),
					'cost'         => $item->get_cost(),
					'weight'       => $item->get_weight(),
					'weight_gross' => $item->get_weight_gross(),
					'amount'       => $item->get_amount()
				), $item, $this->packages, $this );
			}

			$package_number = Woodev_Helper::str_truncate( $package->get_number(), 20, '' );
			$names[]        = $package_number;
			$count_names    = array_count_values( $names );

			$packages[ $index ] = apply_filters( 'wc_edostavka_order_package', array(
				'key'    => $package->get_unique_key(),
				'number' => sprintf( '%s [#%d]', $package_number, $count_names[ $package_number ] ),
				'weight' => $package->get_weight(),
				'length' => $package->get_length(),
				'width'  => $package->get_width(),
				'height' => $package->get_height(),
				'items'  => $items
			), $package );
		}

		return $packages;
	}

	private function create_packages() {
		/** @var WC_Order_Item_Product $item */
		foreach ( $this->items as $item ) {

			$product = $item->get_product();

			if ( ! $product->needs_shipping() ) {
				continue;
			}

			$product_weight = $product->has_weight() ? wc_get_weight( $product->get_weight(), 'g' ) : $this->get_default_dimension( 'weight' );
			$product_height = $product->get_height() > 0 ? wc_get_dimension( $product->get_height(), 'cm' ) : $this->get_default_dimension( 'height' );
			$product_width  = $product->get_width() > 0 ? wc_get_dimension( $product->get_width(), 'cm' ) : $this->get_default_dimension( 'width' );
			$product_length = $product->get_length() > 0 ? wc_get_dimension( $product->get_length(), 'cm' ) : $this->get_default_dimension( 'length' );

			for ( $i = 0; $i < $item->get_quantity(); $i ++ ) {

				$internal_data = array( 'item' => $item );

				$new_item = new Woodev_Packer_Item_Implementation(
					ceil( $product_length ),
					ceil( $product_width ),
					ceil( $product_height ),
					intval( $product_weight ),
					'cod' == $this->order->get_payment_method() ? $this->order->get_item_total( $item ) : 0,
					$internal_data
				);

				$new_item->set_product( $product );

				$this->packer->add_item( $new_item );
			}
		}

		$packaged = $this->packer->get_packages();

		foreach ( $packaged['packages'] as $package ) {
			/** @var WC_Edostavka_Order_Item[] $items */
			$items = array();

			foreach ( ( array ) $package['packed_items'] as $packed_item ) {

				/** @var WC_Order_Item_Product $item */
				$item    = $packed_item['internal_data']['item'];
				$item_id = $item->get_id();
				$product = $item->get_product();

				if ( ! in_array( $item_id, array_keys( $items ), true ) ) {

					$war_key  = ! empty( $product->get_sku() ) ? $product->get_sku() : $product->get_id();
					$value    = $packed_item['value'];
					$services = $this->shipping_method->get_option( 'services', array() );
					$cost     = ( ( $services && in_array( 'INSURANCE', $services, true ) ) || $value > 0 ) ? $this->order->get_item_total( $item ) : 0;

					$items[ $item->get_id() ] = new WC_Edostavka_Order_Item( $item->get_name(), $war_key, $value, $cost, $packed_item['weight'], $packed_item['weight'] );

				} else {
					$items[ $item->get_id() ]->set_amount( $items[ $item->get_id() ]->get_amount() + 1 );
				}
			}

			$new_package = new WC_Edostavka_Order_Package( $package['box']['id'], $package['box']['name'], $package['weight'], $package['box']['length'], $package['box']['width'], $package['box']['height'], $items );

			$this->packages[] = $new_package;
		}
	}

	private function get_default_dimension( $unit ) {

		$dimensions = array(
			'weight' => wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_weight' ),
			'height' => wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_height' ),
			'width'  => wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_width' ),
			'length' => wc_edostavka_shipping()->get_integration_handler()->get_option( 'default_length' )
		);

		if ( in_array( $unit, array_keys( $dimensions ), true ) ) {
			return $dimensions[ $unit ];
		}

		return null;
	}
}
