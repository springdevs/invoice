<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Handle Admin MetaBoxes
 *
 * Class MetaBoxes
 *
 * @package SpringDevs\WcPips\Admin
 */
class MetaBoxes {


	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'add_meta_boxes', array( $this, 'create_meta_boxes' ) );
		add_action( 'save_post_shop_order', array( $this, 'save_invoice_meta' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_invoice_meta' ), 60 );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'pips_admin_css' );
	}

	public function create_meta_boxes() {
		$screen = pips_wc_order_hpos_enabled()
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';
		// Sidebar [ pdf buttons & forms ]
		if ( 'yes' === get_option( 'pips_enable_invoice', 'yes' ) ) :
			add_meta_box(
				'pips_order_action',
				__( 'Order Invoices', 'sdevs_pips' ),
				array( $this, 'order_action_html' ),
				$screen,
				'side',
				'default'
			);
		endif;
	}

	/**
	 * Display single order invoice button.
	 *
	 * @param \WC_Order|\WP_Post $post Post.
	 */
	public function order_action_html( $post ) {
		$order = null;
		if ( $post instanceof \WC_Order ) {
			$order = $post;
		} else {
			$order = wc_get_order( $post->ID );
		}
		$invoice_number = $order->get_meta( '_pips_order_invoice_number' ) ? $order->get_meta( '_pips_order_invoice_number' ) : null;
		$invoice_date   = $order->get_meta( '_pips_order_invoice_number' ) ? $order->get_meta( '_pips_order_invoice_date' ) : null;
		if ( $invoice_date != null ) {
			$invoice_date = date( 'Y-m-d', $invoice_date );
		}

		$invoice_note = $order->get_meta( '_pips_order_invoice_number' ) ? $order->get_meta( '_pips_order_invoice_note' ) : null;

		$invoice_link = 'admin.php?page=pips_view_pdf&view=pips_invoice&post=' . $order->get_id();
		$packing_link = 'admin.php?page=pips_view_pdf&view=pips_packing_slip&post=' . $order->get_id();
		if ( pips_pro_activated() ) {
			do_action( 'pipspro_load_order_action_html', $order, $invoice_link, $packing_link );
		} else {
			include_once 'views/invoice-buttons.php';
		}
		include_once 'views/order-form.php';
	}

	public function save_invoice_meta( $post_id ) {
		if ( ! isset( $_POST['pips_invoice_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['pips_invoice_nonce'], 'pips_order_edit_invoice' ) ) {
			wp_die( 'Undefined nonce !!' );
		}
		$invoice_number = sanitize_text_field( $_POST['pips_invoice_number'] );
		$invoice_date   = sanitize_text_field( $_POST['pips_invoice_date'] );
		$invoice_date   = strtotime( $invoice_date );
		$invoice_note   = sanitize_text_field( $_POST['pips_invoice_note'] );

		$order = wc_get_order( $post_id );
		if ( $order ) {
			$order->update_meta_data( '_pips_order_invoice_number', $invoice_number );
			$order->update_meta_data( '_pips_order_invoice_date', $invoice_date );
			$order->update_meta_data( '_pips_order_invoice_note', $invoice_note );

			$order->save();
		}
	}
}
