<?php

namespace SpringDevs\WcPips;

use Dompdf\Dompdf;

/**
 * The Ajax class
 */
class Ajax {


	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'wp_ajax_pips_save_order_pdf', array( $this, 'pips_save_order_pdf' ) );
		add_action( 'wp_ajax_install_woocommerce_plugin', array( $this, 'install_woocommerce_plugin' ) );
		add_action( 'wp_ajax_activate_woocommerce_plugin', array( $this, 'activate_woocommerce_plugin' ) );
	}

	public function pips_save_order_pdf() {
		$dompdf  = new Dompdf();
		$html    = $this->render_template( PIPS_PATH . '/templates/simple/template.php' );
		$options = $dompdf->getOptions();
		$options->set( 'chroot', PIPS_PATH );
		$dompdf->setOptions( $options );
		$dompdf->loadHtml( $html );
		$dompdf->setPaper( 'A4', 'portrait' );
		$dompdf->render();
		$dompdf->stream( 'hello world', array( 'Attachment' => 0 ) );
		wp_send_json( $_POST );
	}

	public function render_template( $file ) {
		ob_start();
		if ( file_exists( $file ) ) {
			include $file;
		}
		return ob_get_clean();
	}

	public function install_woocommerce_plugin() {
		include ABSPATH . 'wp-admin/includes/plugin-install.php';
		include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/misc.php';

		if ( ! class_exists( 'Plugin_Upgrader' ) ) {
			include ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
		}
		if ( ! class_exists( 'Plugin_Installer_Skin' ) ) {
			include ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php';
		}

		$plugin = 'woocommerce';

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin,
				'fields' => array(
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			wp_die( $api );
		}

		$title = sprintf( __( 'Installing Plugin: %s' ), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . $plugin;
		$url   = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );

		$upgrader = new \Plugin_Upgrader( new \Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
		$upgrader->install( $api->download_link );
		wp_send_json(
			array(
				'msg' => 'Installed successfully !!',
			)
		);
	}

	public function activate_woocommerce_plugin() {
		activate_plugin( 'woocommerce/woocommerce.php' );
		wp_send_json(
			array(
				'msg' => 'Activated successfully !!',
			)
		);
	}
}
