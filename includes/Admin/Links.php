<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Handle plugin action links
 *
 * Class Links
 *
 * @package SpringDevs\WcPips\Admin
 */
class Links {


	public function __construct() {
		add_filter( 'plugin_action_links_' . plugin_basename( PIPS_FILE ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links array
	 */
	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=pips' ) . '">' . __( 'Settings', 'sdevs_pips' ) . '</a>';
		if ( ! pips_pro_activated() ) {
			$links[] = '<a href="https://springdevs.com/plugin/invoice" target="_blank" style="color:#3db634;">' . __( 'Upgrade to premium', 'sdevs_pips' ) . '</a>';
		}
		$links[] = '<a href="https://wordpress.org/support/plugin/pdf-invoices-and-packing-slips" target="_blank">' . __( 'Support', 'sdevs_pips' ) . '</a>';
		$links[] = '<a href="https://wordpress.org/support/plugin/pdf-invoices-and-packing-slips/reviews/?rate=5#new-post" target="_blank">' . __( 'Review', 'sdevs_pips' ) . '</a>';
		return $links;
	}
}
