<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pips_Invoice_Settings' ) ) :

	/**
	 * Settings class
	 */
	class Pips_Invoice_Settings extends \WC_Settings_Page {

		public function __construct() {
			$this->id    = 'pips';
			$this->label = __( 'Invoices', 'sdevs_pips' );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		}

		public function get_sections() {
			$sections = array();

			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}
	}

endif;

return new Pips_Invoice_Settings();
