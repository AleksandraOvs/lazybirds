<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_API_Request extends Woodev_API_JSON_Request {

	use Woodev_Cacheable_Request_Trait;

	public function get_tariff( $params ) {
		$this->path = '/calculator/tariff';
		$this->method = 'POST';
		$this->params = $params;
	}

	public function get_deliverypoints( $params ) {
		$this->path = '/deliverypoints';
		$this->method = 'GET';
		$this->params = $params;
	}

	public function get_location_regions( $params ) {
		$this->path = '/location/regions';
		$this->method = 'GET';
		$this->params = $params;
	}

	public function get_location_cities( $params ) {
		$this->path = '/location/cities';
		$this->method = 'GET';
		$this->params = $params;
	}

	public function get_location_postcodes( $code ) {
		$this->path = '/location/postalcodes';
		$this->method = 'GET';
		$this->params = array( 'code' => $code );
	}

	public function create_order( $params ) {
		$this->path = '/orders';
		$this->method = 'POST';
		$this->params = $params;
	}

	public function get_order( $order_uuid, $params ) {
		$this->path = "/orders/{$order_uuid}";
		$this->method = 'GET';
		$this->params = $params;
	}

	public function update_order( $params ) {
		$this->path = '/orders';
		$this->method = 'PATCH';
		$this->params = $params;
	}

	public function remove_order( $order_uuid, $params ) {
		$this->path = "/orders/{$order_uuid}";
		$this->method = 'DELETE';
		$this->params = $params;
	}

	public function refund_order( $order_uuid, $params ) {
		$this->path = "/orders/{$order_uuid}/refusal";
		$this->method = 'POST';
		$this->params = $params;
	}

	public function create_webhook( $type, $url ) {
		$this->path = '/webhooks';
		$this->method = 'POST';
		$this->params = array(
			'type'  => $type,
			'url'   => $url
		);
	}

	public function get_webhook( $id ) {
		$this->path = "/webhooks/{$id}";
		$this->method = 'GET';
	}

	public function delete_webhook( $id ) {
		$this->path = "/webhooks/{$id}";
		$this->method = 'DELETE';
	}

	public function call_courier( $params ) {
		$this->path = '/intakes';
		$this->method = 'POST';
		$this->params = $params;
	}

	public function get_order_waybill( $uuid ) {
		$this->path = "/print/orders/{$uuid}";
		$this->method = 'GET';
	}

	public function create_order_waybill( $ids ) {
		$this->path = "/print/orders";
		$this->method = 'POST';

		$this->params = apply_filters( 'wc_edostavka_create_order_waybill_params', array(
			'copy_count'    => 2,
			'orders'        => $ids
		) );
	}

	public function create_order_barcode( $ids ) {
		$this->path = "/print/barcodes";
		$this->method = 'POST';

		$this->params = apply_filters( 'wc_edostavka_create_order_barcode_params', array(
			'copy_count'    => 1,
			'orders'        => $ids
		) );
	}

	public function get_print_file( $uuid, $type ) {
		$path_type = $type == 'waybill' ? 'orders' : 'barcodes';
		$this->path = "/print/{$path_type}/{$uuid}.pdf";
		$this->method = 'GET';
	}

	public function get_path() {

		$path   = $this->path;
		$params = $this->get_params();

		if ( 'GET' === $this->get_method() && ! empty( $params ) ) {

			$path .= '?' . http_build_query( $this->get_params(), '', '&' );
		}

		return $path;
	}

	public function to_string() {

		if ( 'GET' === $this->get_method() ) {
			return array();
		} elseif( in_array( $this->get_method(), array( 'POST', 'PUT', 'DELETE' ), true ) ) {
			return wp_json_encode( $this->get_params() );
		} else {
			return http_build_query( $this->get_params() );
		}
	}
}
