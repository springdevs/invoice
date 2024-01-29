<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Handle Order related feature
 *
 * Class Order
 *
 * @package SpringDevs\WcPips\Admin
 */
class Order {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		if ( 'yes' === get_option( 'pips_order_custom_column', 'yes' ) ) :
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_custom_columns' ) );
			add_filter( 'woocommerce_shop_order_list_table_columns', array( $this, 'add_custom_columns' ) );

			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_custom_columns_data' ), 10, 2 );
			add_action( 'init', array( $this, 'load_hpos_hooks' ) );
		endif;
	}

	/**
	 * Load HPOS hooks here else `wc_get_page_screen_id` isn't available.
	 */
	public function load_hpos_hooks() {
		add_action( 'manage_' . wc_get_page_screen_id( 'shop_order' ) . '_custom_column', array( $this, 'add_custom_columns_data' ), 10, 2 );
	}

	/**
	 * Add Custom Column to Orders Table.
	 *
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	public function add_custom_columns( $columns ) {
		$columns['pips_order_column'] = __( 'Invoice', 'sdevs_pips' );
		return $columns;
	}

	/**
	 * Add Custom Column Data.
	 *
	 * @param string        $column Column ID.
	 * @param int|\WC_Order $post_id post_id or Order Obj.
	 */
	public function add_custom_columns_data( $column, $post_id ) {
		if ( 'pips_order_column' === $column ) :
			// check if post_id is order object.
			if ( 'object' === gettype( $post_id ) ) {
				$post_id = $post_id->get_id();
			}
			$invoice_view_link = 'admin.php?page=pips_view_pdf&view=pips_invoice&post=' . $post_id;
			?>
			<a style="margin-right: 10px;" href="<?php echo esc_html( $invoice_view_link ); ?>" target="_blank">
				<span class="dashicons dashicons-welcome-view-site"></span>
			</a>
			<a href="<?php echo esc_html( $invoice_view_link . '&download=true' ); ?>" target="_blank">
				<span class="dashicons dashicons-database-import"></span>
			</a>
			<?php
		endif;
	}
}
