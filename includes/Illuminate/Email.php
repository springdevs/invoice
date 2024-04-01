<?php

namespace SpringDevs\WcPips\Illuminate;

/**
 * Handle Emails
 *
 * Class Email
 *
 * @package SpringDevs\WcPips\Illuminate
 */
class Email {

	public function __construct() {
		add_action( 'init', array( $this, 'clean_directory' ) );
	}

	public function clean_directory() {
		$path_options = get_option( 'pips_save_pdfs', array() );
		foreach ( $path_options as $file_path ) {
			if ( file_exists( $file_path ) ) {
				unlink( $file_path );
			}
		}
		delete_option( 'pips_save_pdfs' );
	}
}
