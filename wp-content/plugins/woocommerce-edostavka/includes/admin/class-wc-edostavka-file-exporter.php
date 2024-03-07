<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Edostavka_File_Exporter {

	private $file;

	private $filename = 'file.pdf';

	private $data;

	public function __construct( $raw_data, $attr = array() ) {
		$upload_dir         = wp_upload_dir();
		$this->file         = trailingslashit( $upload_dir['basedir'] ) . $this->get_filename();
		$this->data         = $raw_data;
		if( $attr['file_name'] ) {
			$this->set_filename( esc_attr( $attr['file_name'] ) );
		}
	}

	public function set_filename( $filename ) {
		$this->filename = sanitize_file_name( str_replace( '.pdf', '', $filename ) . '.pdf' );
	}

	public function get_filename() {
		return sanitize_file_name( $this->filename );
	}

	public function generate_file() {
		@file_put_contents( $this->file, $this->data );
	}

	private function get_file() {
		$file = '';
		if ( @file_exists( $this->file ) ) {
			$file = @file_get_contents( $this->file );
		} else {
			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );
		}
		return $file;
	}

	public function export() {
		$this->send_headers();
		echo $this->get_file();
		@unlink( $this->file );
		die();
	}

	private function send_headers() {
		if ( function_exists( 'gc_enable' ) ) {
			gc_enable();
		}
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}
		@ini_set( 'zlib.output_compression', 'Off' );
		@ini_set( 'output_buffering', 'Off' );
		@ini_set( 'output_handler', '' );
		ignore_user_abort( true );
		wc_set_time_limit( 0 );
		nocache_headers();
		header( 'Content-Type: application/pdf; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}
}
