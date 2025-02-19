<?php

namespace SpringDevs\WcPips\Illuminate;

/**
 *
 * Class Invoice
 *
 * @package SpringDevs\WcPips\Illuminate
 */
class Invoice {

	/**
	 * Order object.
	 *
	 * @var \WC_Order $order Order.
	 */
	public $order;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'pips_product_column_product', array( $this, 'product_content' ) );
		add_action( 'pips_product_column_qty', array( $this, 'product_qty' ) );
		add_action(
			'pips_product_column_subtotal',
			array( $this, 'product_subtotal' ),
			10,
			2
		);
		add_filter(
			'woocommerce_email_attachments',
			array( $this, 'include_pdf_with_email' ),
			10,
			3
		);
	}

	/**
	 * Attach invoice with email order.
	 *
	 * @param array     $attachments Attachments.
	 * @param int       $email_id Email id.
	 * @param \WC_Order $order Order data.
	 *
	 * @return array
	 */
	public function include_pdf_with_email( $attachments, $email_id, $order ) {
		$email_ids = array( 'customer_invoice' );
		if (
			'yes' === get_option( 'pips_free_order_invoice', 'no' ) &&
			0 === $order->get_total()
		) {
			return $attachments;
		}

		if ( 'yes' === get_option( 'pips_attach_pdf_new_order_admin', 'yes' ) ) {
			$email_ids[] = 'new_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_cancelled_order', 'no' ) ) {
			$email_ids[] = 'cancelled_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_failed_order', 'no' ) ) {
			$email_ids[] = 'failed_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_on-hold_order', 'no' ) ) {
			$email_ids[] = 'customer_on_hold_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_processing_order', 'no' ) ) {
			$email_ids[] = 'customer_processing_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_completed_order', 'no' ) ) {
			$email_ids[] = 'customer_completed_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_refunded_order', 'no' ) ) {
			$email_ids[] = 'customer_refunded_order';
		}
		if ( 'yes' === get_option( 'pips_attach_pdf_admin_note_order', 'no' ) ) {
			$email_ids[] = 'customer_note';
		}

		// $disable_statuses = get_option( 'pips_invoice_disable_statuses', array() );
		// foreach ( $disable_statuses as $disable_statuse ) {
		// if ( $order->has_status( substr( $disable_statuse, 3 ) ) ) {
		// return $attachments;
		// }
		// }

		if ( in_array( $email_id, $email_ids, true ) ) {
			$file_path = $this->generate_save_pdf( $order->get_id() );
			if ( file_exists( $file_path ) ) {
				$attachments[]  = $file_path;
				$path_options   = get_option( 'pips_save_pdfs', array() );
				$path_options[] = $file_path;
				update_option( 'pips_save_pdfs', $path_options );
			}
		}
		return $attachments;
	}

	/**
	 * Display data on product column.
	 *
	 * @param \WC_Order_Item_Product $item Order Item.
	 *
	 * @return void
	 */
	public function product_content( $item ) {
		$product = $item->get_product(); ?>
		<td class="product-content">
			<span class="item-name"><?php echo esc_html( $item->get_name() ); ?></span>
			<?php if ( $this->get_product_sku( $product ) ) : ?>
				<dl class="meta"><small>SKU: 
				<?php
				echo esc_html(
					$this->get_product_sku( $product )
				);
				?>
	</small></dl>
			<?php endif; ?>
		</td>
		<?php
	}

	/**
	 * Display data on qty column.
	 *
	 * @param \WC_Order_Item_Product $item Order Item.
	 *
	 * @return void
	 */
	public function product_qty( $item ) {
		?>
		<td>
			<?php echo esc_html( $item->get_quantity() ); ?>
		</td>
		<?php
	}

	/**
	 * Display product subtotal on subtotal column.
	 *
	 * @param \WC_Order_Item $item Order Item.
	 * @param \WC_Order      $order Order Object.
	 *
	 * @return void
	 */
	public function product_subtotal( $item, $order ) {
		$product_variation_id = $item['variation_id'];
		// Check if product has variation.
		if ( $product_variation_id ) {
			$product = wc_get_product( $item['variation_id'] );
		} else {
			$product = wc_get_product( $item['product_id'] );
		}
		?>
		<td>
			<?php
			echo wp_kses_post(
				apply_filters(
					'woocommerce_get_price_html',
					$this->get_line_subtotal( $order, $item ),
					$product
				)
			);
			?>
		</td>
		<?php
	}

	/**
	 * Register menu page for admin.
	 *
	 * @return void
	 */
	public function admin_menu() {
		$hook = add_submenu_page(
			null,
			'Preview PDF',
			'Preview PDF',
			'manage_options',
			'pips_view_pdf',
			function () {}
		);
		add_action( 'load-' . $hook, array( $this, 'generate_order_pdf' ) );
	}

	/**
	 * Generate PDF for Order.
	 *
	 * @return void
	 */
	public function generate_order_pdf() {
		do_action( 'pips_pdf_generator', $this );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['view'], $_GET['post'] ) ) {
			return;
		}

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$view = sanitize_text_field( wp_unslash( $_GET['view'] ) );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order = wc_get_order( sanitize_text_field( wp_unslash( $_GET['post'] ) ) );
		if ( ! $order ) {
			return;
		}
		$this->order = $order;

		if (
			'pips_invoice' === $view &&
			'yes' === get_option( 'pips_enable_invoice', 'yes' )
		) {
			$invoice_template_path = pips_invoice_template_path();
			$html                  = $this->render_template(
				$invoice_template_path . '/template.php'
			);
			$name                  = $this->get_invoice_file_name();

			$generator = pips_get_generator_instance();
			$generator->WriteHTML( $html );
			$generator->SetTitle( $name );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$generator->Output(
				$name . '.pdf',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				isset( $_GET['download'] ) && 'true' === $_GET['download']
					? 'D'
					: 'I'
			);
			exit();
		} elseif (
			'pips_packing_slip' === $view &&
			'yes' === get_option( 'pips_enable_packing_slip', 'yes' )
		) {
			$packing_template_path = pips_packing_template_path();
			$html                  = $this->render_template(
				$packing_template_path . '/template.php',
				array( 'order' => $order )
			);
			$name                  = 'packing-slip-' . $this->get_invoice_number();
			$generator             = pips_get_generator_instance( 'packing' );
			$generator->WriteHTML( $html );
			$generator->SetTitle( $name );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$generator->Output(
				$name . '.pdf',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				isset( $_GET['download'] ) && 'true' === $_GET['download']
					? 'D'
					: 'I'
			);
			exit();
		}
	}

	/**
	 *
	 * Generate and save pdf for email.
	 *
	 * @param int $order_id Order id.
	 *
	 * @return string
	 */
	public function generate_save_pdf( $order_id ) {
		$order                 = wc_get_order( $order_id );
		$this->order           = $order;
		$invoice_template_path = pips_invoice_template_path();

		$html      = $this->render_template(
			$invoice_template_path . '/template.php'
		);
		$generator = pips_get_generator_instance();
		$generator->WriteHTML( $html );

		$upload_dir  = wp_upload_dir();
		$upload_path = $upload_dir['basedir'] . '/pips';
		if ( ! file_exists( $upload_path ) ) {
			mkdir( $upload_path, 0777, true );
		}

		$file_path =
			$upload_path . '/invoice-' . $this->get_invoice_number() . '.pdf';
		$generator->Output( $file_path, 'F' );

		return $file_path;
	}

	/**
	 * Render template.
	 *
	 * @param string $file File path.
	 *
	 * @return string<HTML>|false
	 */
	public function render_template( $file ) {
		ob_start();
		if ( file_exists( $file ) ) {
			include $file;
		}
		return ob_get_clean();
	}

	/**
	 * Return formatted store address.
	 *
	 * @return string
	 */
	public function get_formatted_store_address() {
		return WC()->countries->get_formatted_address(
			array(
				'address_1' => WC()->countries->get_base_address(),
				'address_2' => WC()->countries->get_base_address_2(),
				'city'      => WC()->countries->get_base_city(),
				'state'     => WC()->countries->get_base_state(),
				'postcode'  => WC()->countries->get_base_postcode(),
				'country'   => WC()->countries->get_base_country(),
			)
		);
	}

	/**
	 * Get customer details [placeholder].
	 *
	 * @return string
	 */
	public function get_customer_details() {
		return apply_filters( 'pips_get_customer_details', 'N/A', $this->order );
	}

	/**
	 * Get shipping details [placeholder].
	 *
	 * @return string
	 */
	public function get_shipping_details() {
		return apply_filters( 'pips_get_shipping_details', 'N/A', $this->order );
	}

	/**
	 * Get shop name.
	 *
	 * @return string
	 */
	public function get_shop_name() {
		if ( get_option( 'pips_invoice_shop_name' ) ) {
			return get_option( 'pips_invoice_shop_name' );
		}
		return get_bloginfo( 'name' );
	}

	/**
	 * Get invoice file name.
	 *
	 * @return string
	 */
	public function get_invoice_file_name() {
		$invoice_number = $this->get_invoice_number();

		$file_name_format = trim(
			get_option( 'pips_invoice_file_name_format', 'invoice-[number]' )
		);
		preg_match_all( '/\[([^\]]*)\]/', $file_name_format, $matches );
		$matches = $matches[1];
		foreach ( $matches as $match ) {
			if ( 'number' === $match ) {
				$file_name_format = str_replace(
					'[' . $match . ']',
					$invoice_number,
					$file_name_format
				);
			}
		}

		return $file_name_format;
	}

	/**
	 * Get Invoice Number.
	 *
	 * @return string
	 */
	public function get_invoice_number() {
		$invoice_number = $this->order->get_meta( '_pips_order_invoice_number' );

		if ( empty( $invoice_number ) ) {
			$invoice_number = $this->order->get_id();
			if ( 'sequential' === get_option( 'pips_invoice_number_type', 'order_id' ) ) {
				$start_from       = get_option( 'pips_invoice_number_start', 1 );
				$previous_invoice = get_option( 'pips_last_invoice_number', $start_from );
				$invoice_number   = $previous_invoice + 1;
				update_option( 'pips_last_invoice_number', $invoice_number );
			}
			$this->order->update_meta_data( '_pips_order_invoice_number', $invoice_number );
			$this->order->save();
		}

		return $invoice_number;
	}

	/**
	 * Get formatted invoice number.
	 *
	 * @return string
	 */
	public function get_formatted_invoice_number() {
		$invoice_number = $this->get_invoice_number();

		$invoice_format = trim(
			get_option( 'pips_invoice_number_format', '[number]' )
		);
		preg_match_all( '/\[([^\]]*)\]/', $invoice_format, $matches );
		$matches = $matches[1];
		foreach ( $matches as $match ) {
			if ( 'number' === $match ) {
				$invoice_format = str_replace(
					'[' . $match . ']',
					$invoice_number,
					$invoice_format
				);
			}
		}

		return $invoice_format;
	}

	/**
	 * Get Invoice Date.
	 *
	 * @return string
	 */
	public function get_invoice_date() {
		$order_meta = $this->order->get_meta( '_pips_order_invoice_date' );
		if ( ! empty( $order_meta ) ) {
			return gmdate(
				get_option( 'pipspro_invoice_date_format', 'F d, Y' ),
				$order_meta
			);
		}
		$default_date = gmdate(
			get_option( 'pipspro_invoice_date_format', 'F d, Y' ),
			strtotime( $this->order->get_date_created() )
		);

		return apply_filters(
			'pips_filter_invoice_date',
			$default_date,
			$this->order
		);
	}

	/**
	 * Get product columns for invoice.
	 *
	 * @return array
	 */
	public function get_product_invoice_columns() {
		$columns = array(
			'product'  => __( 'Product', 'sdevs_pips' ),
			'qty'      => __( 'Qty', 'sdevs_pips' ),
			'subtotal' => __( 'Subtotal', 'sdevs_pips' ),
		);

		return apply_filters(
			'pips_invoice_product_columns',
			$columns,
			$this->order
		);
	}

	/**
	 * Get blank columns number.
	 *
	 * @return int
	 */
	public function get_blank_columns() {
		return count( $this->get_product_invoice_columns() ) - 2;
	}

	/**
	 * Get invoice note.
	 *
	 * @return string
	 */
	public function get_invoice_note() {
		$order_meta = $this->order->get_meta( '_pips_order_invoice_note' );
		if ( $order_meta ) {
			return $order_meta;
		}
		if ( get_option( 'pips_invoice_note' ) ) {
			return get_option( 'pips_invoice_note' );
		}
		return false;
	}

	/**
	 * Get packing note.
	 *
	 * @return string
	 */
	public function get_packing_note() {
		$order_meta = $this->order->get_meta( '_pips_order_packing_note' );
		if ( $order_meta ) {
			return $order_meta;
		}
		if ( get_option( 'pips_packing_slip_note' ) ) {
			return get_option( 'pips_packing_slip_note' );
		}
		return false;
	}

	/**
	 * Get footer note.
	 *
	 * @return string
	 */
	public function get_footer_note() {
		if ( get_option( 'pips_invoice_footer_note' ) ) {
			return get_option( 'pips_invoice_footer_note' );
		}
		return false;
	}

	/**
	 * Has shipping address on Order ?
	 *
	 * @return bool
	 */
	public function has_shipping_address(): bool {
		if ( ! $this->order->needs_shipping_address() ) {
			return false;
		}
		$setting = get_option(
			'pips_invoice_display_shipping_address',
			'when_different'
		);
		if ( 'no' === $setting ) {
			return false;
		}
		if ( 'always' === $setting ) {
			return true;
		}
		return $this->order->has_shipping_address() &&
			$this->order->get_billing_address_1() !==
				$this->order->get_shipping_address_1();
	}

	/**
	 * Get product sku [placeholder].
	 *
	 * @param \WC_Product $product Product Object.
	 *
	 * @return string
	 */
	public function get_product_sku( $product ) {
		$sku = $product->get_sku();
		return apply_filters( 'pips_product_sku', $sku, $product );
	}

	/**
	 * Get line subtotal.
	 *
	 * @param \WC_Order              $order Order Object.
	 * @param \WC_Order_Item_Product $item Order Item.
	 *
	 * @return string
	 */
	public function get_line_subtotal( $order, $item ) {
		$single_price  = $order->get_item_subtotal( $item, false, true );
		$regular_price = $single_price * $item->get_quantity();
		$sale_price    = $item->get_total();
		$currency_code = $order->get_currency();
		if (
			$regular_price != $sale_price &&
			'yes' === get_option( 'pipspro_invoice_slashed_price', 'yes' )
		) {
			return wc_format_sale_price( $regular_price, $sale_price );
		}
		return pips_price( $sale_price, $currency_code );
	}
}
