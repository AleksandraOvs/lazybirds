<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_Box_Packer {

	/**
	 * @var Woodev_Packer $packer
	 */
	private $packer;

	/**
	 * @var Woodev_Packer $cannot_pack_method
	 */
	private $cannot_pack_method;

	public function __construct( Woodev_Packer $packer ) {
		$this->packer = $packer;
	}

	public function add_box( Woodev_Box_Packer_Box $box ) {
		$this->packer->add_box( $box );
	}

	public function add_item( Woodev_Box_Packer_Item $item ) {
		$this->packer->add_item( $item );
	}

	public function set_cannot_pack_method( Woodev_Packer $cannot_packer ) {
		$this->cannot_pack_method = $cannot_packer;
	}

	/**
	 * Get all packaged boxes
	 *
	 * @return array
	 */
	public function get_packages() {

		$packaged_boxes = array(
			'packages'      => array(),
			'total_weight'  => 0
		);

		try {
			$this->packer->pack();
		} catch ( Woodev_Packer_Exception $e ) {
			wc_edostavka_shipping()->log( $e->getMessage() );
			return $packaged_boxes;
		}

		$packages = $this->packer->get_packages();
		$items_cannot_pack = $this->packer->get_items_cannot_pack();

		if( $items_cannot_pack ) {

			$cannot_packer = $this->cannot_pack_method && ! is_a( $this->cannot_pack_method, 'Woodev_Packer_Boxes' ) ? $this->cannot_pack_method : new Woodev_Packer_Separately( __( 'Separately Box', 'woocommerce-edostavka' ) );

			foreach ( $items_cannot_pack as $item ) $cannot_packer->add_item( $item );

			try {
				$cannot_packer->pack();

				$packages = array_merge( $cannot_packer->get_packages(), $packages );

			} catch ( Woodev_Packer_Exception $e ) {
				wc_edostavka_shipping()->log( $e->getMessage() );
			}
		}

		foreach ( $packages as $packed ) {

			$nofit_items = $packed_items = array();

			foreach ( $packed->get_nofit_items() as $item ) $nofit_items[] = $this->item_to_array( $item );

			foreach ( $packed->get_packed_items() as $item ) $packed_items[] = $this->item_to_array( $item );

			$packaged_boxes['packages'][] = array(
				'box'   => $this->box_to_array( $packed->get_box() ),
				'nofit_items' => $nofit_items,
				'packed_items' => $packed_items,
				'weight' => $packed->get_packed_weight(),
				'percent' => $packed->get_success_percent(),
				'value' => $packed->get_packed_value()
			);
		}

		$total_weight = new Woodev_Box_Packer_Packages_Weight( $packages );
		$packaged_boxes['total_weight'] = $total_weight->get_total_weight();

		return $packaged_boxes;
	}

	private function item_to_array( Woodev_Box_Packer_Item $item ) {
		return array(
			'length'        => $item->get_length(),
			'width'         => $item->get_width(),
			'height'        => $item->get_height(),
			'value'         => $item->get_value(),
			'volume'        => $item->get_volume(),
			'weight'        => $item->get_weight(),
			'internal_data' => $item->get_internal_data()
		);
	}

	private function box_to_array( Woodev_Box_Packer_Box $box ) {
		return array_merge(
			array(
				'id'            => $box->get_unique_id(),
				'name'          => $box->get_name(),
				'max_weight'    => $box->get_max_weight()
			),
			$this->item_to_array( $box )
		);
	}
}
