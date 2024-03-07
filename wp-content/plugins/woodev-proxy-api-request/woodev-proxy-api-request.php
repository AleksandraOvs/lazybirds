<?php
/**
 * Plugin Name: Woodev Proxy API Requests
 * Plugin URI: https://woodev.ru/
 * Description: Проксирует запросы к серверу Woodev через внешний IP адрес.
 * Version: 1.0.0
 * Author: WooDev
 * Author URI: https://woodev.ru
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Проксирует запросы через внешний IP сервера.
 */
add_action( 'http_api_curl', function( $handle, $params, $url ) {
    if( ! empty( $url ) && 0 === strpos( $url, 'https://woodev.ru' ) && array_key_exists( 'SERVER_ADDR', $_SERVER ) && ! empty( $_SERVER['SERVER_ADDR'] ) ) {
		curl_setopt( $handle, CURLOPT_INTERFACE, $_SERVER['SERVER_ADDR'] );
	}
}, 10, 3 );

add_action( 'admin_init', function() {
	if( isset( $_GET['check_outgoung_ip'] ) ) {
		
		$response = wp_remote_get( 'https://ipinfo.io/json' );
		
		if( ! is_wp_error( $response ) ) {
			
			$body = wp_remote_retrieve_body( $response );
			
			printf( '<pre>%s</pre>', wc_print_r( $body, true ) );
		
		} else {
			
			echo $response->get_error_message();
		}	
					
		die;
	}
} );