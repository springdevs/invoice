<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Install & Activate required plugins
 *
 * Class Menu
 */
class Required {

	private $plugin_file = true;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'init', array( $this, 'check_plugins' ) );
	}

	public function check_plugins() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_file = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';

		if ( ! file_exists( $plugin_file ) ) {
			$this->plugin_file = false;
		}

		if ( ! file_exists( $plugin_file ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'install_plugin_notice' ) );
		}
	}

	public function enqueue_assets() {
		if ( ! wp_style_is( 'sdevs_installer' ) ) {
			wp_enqueue_style( 'sdevs_installer' );
		}

		if ( ! wp_script_is( 'sdevs_installer' ) ) {
			wp_enqueue_script( 'sdevs_installer' );
			wp_localize_script(
				'sdevs_installer',
				'sdevs_installer_helper_obj',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
			);
		}
	}

	public function install_plugin_notice() {
		if ( $this->plugin_file ) {
			$id    = 'sdevs-activate-plugin';
			$label = __( 'Activate Woocommerce', 'sdevs_pips' );
		} else {
			$id    = 'sdevs-install-plugin';
			$label = __( 'Install Woocommerce', 'sdevs_pips' );
		}

		include 'views/required-notice.php';
	}
}
