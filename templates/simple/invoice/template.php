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
				text-align: left;
			}
			/* .invoice-box table tr td:nth-child(3) {
				text-align: right;
			} */

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding: 20px 0;
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

			.invoice-box table tr.item td.product-content {
				width: 40%;
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
				width: 400px;
			}
			.customer-note {
				padding-top: 20px;
				width: 400px;
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
					<td colspan="<?php echo esc_html( count( $this->get_product_invoice_columns() ) ); ?>">
						<table>
							<tr>
								<td class="title">
								<?php if ( ! empty( get_option( 'pips_invoice_logo' ) ) ) : ?>
									<img
										src="<?php echo esc_html( get_option( 'pips_invoice_logo' ) ); ?>"
										style="width: 100%; max-width: 200px"
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
					<td style="padding-top: 40px;padding-bottom: 20px;text-transform: capitalize;" colspan="3">
						<h1>Invoice</h1>
					</td>
				</tr>

				<tr class="information">
					<td colspan="<?php echo esc_html( count( $this->get_product_invoice_columns() ) ); ?>">
						<table>
							<tr>
								<td>
									<?php echo wp_kses_post( pips_pro_activated() ? $this->get_customer_details() : $this->order->get_formatted_billing_address() ); ?><br/>
									<?php
									if ( 'yes' === get_option( 'pips_display_user_phone', 'no' ) ) {
										echo wp_kses_post( $this->order->get_billing_phone() );
									}
									?>
									<br/>
									<?php
									if ( 'yes' === get_option( 'pips_display_user_email', 'no' ) ) {
										echo wp_kses_post( $this->order->get_billing_email() );
									}
									?>
								</td>
								<td>
									<?php if ( $this->has_shipping_address() ) : ?>
										<h5><?php _e( 'Ship To:', 'sdevs_pips' ); ?></h5>
										<?php echo wp_kses_post( $this->order->has_shipping_address() ? ( pips_pro_activated() ? $this->get_shipping_details() : $this->order->get_formatted_shipping_address() ) : 'N/A' ); ?>
										<?php endif; ?>
								</td>
								<td style="font-size: 10pt;text-align: right;">
									<?php if ( 'yes' === get_option( 'pips_display_invoice_number', 'no' ) ) : ?>
									<b>Invoice #</b> <?php echo esc_html( $this->get_formatted_invoice_number() ); ?><br />
									<?php endif; ?>
									<?php
									if ( 'yes' === get_option( 'pips_display_invoice_date', 'no' ) ) :
										?>
									<b>Invoice Date:</b><br/> <?php echo esc_html( $this->get_invoice_date() ); ?><br />
									<?php endif; ?>
									<b>Order #</b> <?php echo esc_html( $this->order->get_id() ); ?><br />
									<b>Order Date:</b> <br/> <?php echo esc_html( gmdate( get_option( 'pipspro_invoice_date_format', 'F d, Y' ), strtotime( $this->order->get_date_created() ) ) ); ?><br />
									<b>Payment Method:</b> <br/> <?php echo wp_kses_post( $this->order->get_payment_method_title() ); ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<?php foreach ( $this->get_product_invoice_columns()  as $column_key => $column_label ) : ?>
					<td><?php echo esc_html( $column_label ); ?></td>
					<?php endforeach; ?>
				</tr>

				<?php foreach ( $this->order->get_items() as $item_id => $item ) : ?>
				<tr class="item">
					<?php
					foreach ( $this->get_product_invoice_columns() as $column_key => $column_label ) {
						do_action( 'pips_product_column_' . $column_key, $item, $this->order );
					}
					?>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td></td>
				</tr>
				<tr class="total">
					<?php
					for ( $i = 0; $i < ( $this->get_blank_columns() );
					$i++ ) :
						?>
					<td></td>
					<?php endfor; ?>
					<td class="label"><b>Subtotal</b>:</td>
					<td class="value"><?php echo pips_price( $this->order->get_subtotal(), $this->order->get_currency() ); ?></td>
				</tr>
				<tr class="total">
					<?php
					for ( $i = 0; $i < ( $this->get_blank_columns() );
					$i++ ) :
						?>
					<td></td>
					<?php endfor; ?>
					<td class="label"><b>Tax</b>:</td>
					<td class="value"><?php echo pips_price( $this->order->get_total_tax(), $this->order->get_currency() ); ?></td>
				</tr>
				<?php if ( $this->order->get_discount_total() != 0 ) : ?>
				<tr class="total">
					<?php
					for ( $i = 0; $i < ( $this->get_blank_columns() );
					$i++ ) :
						?>
					<td></td>
					<?php endfor; ?>
					<td class="label"><b>Discount</b>:</td>
					<td class="value">- <?php echo wp_kses_post( $this->order->get_discount_to_display() ); ?></td>
				</tr>
				<?php endif; ?>
				<tr class="total">
					<?php
					for ( $i = 0; $i < ( $this->get_blank_columns() );
					$i++ ) :
						?>
					<td></td>
					<?php endfor; ?>
					<td class="label"><b>Total</b>:</td>
					<td class="value"><?php echo pips_price( $this->order->get_total(), $this->order->get_currency() ); ?></td>
				</tr>
			</table>

			<table>
			<?php if ( $this->get_invoice_note() ) : ?>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr class="order-note">
				<td>
					<h4>Notes</h4>
				<p>
					<?php echo wp_kses_post( $this->get_invoice_note() ); ?>
				</p>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( $this->order->get_customer_note() != '' && 'yes' === get_option( 'pips_display_customer_note', 'yes' ) ) : ?>
			<tr class="customer-note">
				<td>
					<h4>Customer Notes</h4>
				<p>
					<?php echo wp_kses_post( $this->order->get_customer_note() ); ?>
				</p>
				</td>
			</tr>
			<?php endif; ?>
			</table>
		</div>
		<?php if ( $this->get_footer_note() ) : ?>
		<footer>
			<?php echo wp_kses_post( $this->get_footer_note() ); ?>
		</footer>
		<?php endif; ?>
	</body>
</html>
