<html>
	<head>
		<meta charset="utf-8" />

		<style>
			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-top: 20px;
				padding-bottom: 40px;
				padding-left: 0;
				padding-right: 0;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(3) {
				/* border-top: 2px solid #eee; */
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
			.order-note {
				padding-top: 60px;
			}
			.customer-note {
				padding-top: 20px;
			}
			footer {
				position: absolute;
				bottom: 10px;
				text-align: center;
				width: 100%;
				left: 0;
				right: 0;
				border-top: 2px solid #eee;
				padding-top: 10px;
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									<?php if ( ! empty( get_option( 'pips_invoice_logo' ) ) ) :
										?>
									<img
										src="<?php echo esc_html( get_option( 'pips_invoice_logo' ) ); ?>"
										style="width: 100%; max-width: 300px"
									/>
									<?php endif; ?>
								</td>
								<?php if ( 'yes' === get_option( 'pipspro_invoice_display_shop', 'yes' ) ) : ?>
								<td style="text-align: right;">
									<h3><?php echo esc_html( $this->get_shop_name() ); ?></h3>
									<?php echo wp_kses_post( $this->get_formatted_store_address() ); ?>
								</td>
								<?php endif; ?>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td style="padding-top: 40px;padding-bottom: 20px;text-transform: capitalize;" colspan="2">
						<h1>Packing Slip</h1>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
									<?php echo wp_kses_post( $this->order->has_shipping_address() ? $this->order->get_formatted_shipping_address() : $this->order->get_formatted_billing_address() ); ?><br/>
									<?php
									if ( 'yes' === get_option( 'pips_packing_slip_display_email', 'no' ) ) {
										echo wp_kses_post( $this->order->get_billing_email() );
									}
									?>
									<br/>
									<?php
									if ( 'yes' === get_option( 'pips_packing_slip_display_phone', 'no' ) ) {
										echo wp_kses_post( $this->order->get_billing_phone() );
									}
									?>
								</td>
								<td style="font-size: 16px;">
									<p><b>Order # </b><?php echo esc_html( $this->order->get_id() ); ?></p>
									<p><b>Order Date:</b> <br/> <?php echo esc_html( gmdate( get_option( 'pipspro_invoice_date_format', 'F d, Y' ), strtotime( $this->order->get_date_created() ) ) ); ?></p>
									<p><b>Shipping Method:</b> <br/> <?php echo wp_kses_post( empty( $this->order->get_shipping_method() ) ? 'N/A' : $this->order->get_shipping_method() ); ?></p>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td><?php esc_html_e( 'Product', 'sdevs_pips' ); ?></td>
					<td><?php esc_html_e( 'Quantity', 'sdevs_pips' ); ?></td>
				</tr>

				<?php foreach ( $this->order->get_items() as $item_id => $item ) : ?>
					<?php
					$product_variation_id = $item['variation_id'];
					// Check if product has variation.
					if ( $product_variation_id ) {
						$product = wc_get_product( $item['variation_id'] );
					} else {
						$product = wc_get_product( $item['product_id'] );
					}
					$sku = $product->get_sku();
					?>
				<tr class="item">
					<td>
						<span class="item-name"><?php echo esc_html( $item->get_name() ); ?></span>
					<dl class="meta"><small>SKU: <?php echo esc_html( $sku ); ?></small></dl>
					</td>
					<td class="quantity"><?php echo esc_html( $item->get_quantity() ); ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php if ( $this->get_packing_note() && 'yes' === get_option( 'pips_packing_slip_display_note', 'no' ) ) : ?>
			<div class="order-note">
				<h4>Notes</h4>
				<p>
					<?php echo wp_kses_post( $this->get_packing_note() ); ?>
				</p>
			</div>
			<?php endif; ?>
			<?php if ( $this->order->get_customer_note() != '' && 'yes' === get_option( 'pips_display_customer_note', 'yes' ) ) : ?>
			<div class="customer-note">
				<h4>Customer Notes</h4>
				<p>
					<?php echo wp_kses_post( $this->order->get_customer_note() ); ?>
				</p>
			</div>
			<?php endif; ?>
		</div>
		<?php if ( $this->get_footer_note() ) : ?>
		<footer>
			<?php echo wp_kses_post( $this->get_footer_note() ); ?>
		</footer>
		<?php endif; ?>
	</body>
</html>
