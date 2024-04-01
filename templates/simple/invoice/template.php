<table class="head container">
	<tr>
		<td class="header">
			<?php if ( get_option( 'pips_invoice_logo_height' ) ) : ?>
				<img height="<?php echo esc_html( get_option( 'pips_invoice_logo_height' ) ); ?>" src="<?php echo esc_html( get_option( 'pips_invoice_logo' ) ); ?>" alt="Logo" />
			<?php else : ?>
				<img src="<?php echo esc_html( get_option( 'pips_invoice_logo' ) ); ?>" alt="Logo" />
			<?php endif; ?>
		</td>
		<td class="shop-info">
			<?php if ( 'yes' === get_option( 'pipspro_invoice_display_shop', 'yes' ) ) : ?>
				<div class="shop-name">
					<h3><?php echo esc_html( $this->get_shop_name() ); ?></h3>
					<?php if ( $this->get_shop_address() ) : ?>
						<p><?php echo esc_html( $this->get_shop_address() ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</td>
	</tr>
</table>

<table class="order-data-addresses">
	<tr>
		<td>
			<h1><?php _e( 'Invoice', 'sdevs_pips' ); ?></h1>
		</td>
	</tr>
	<tr>
		<td class="address billing-address">
			<?php echo wp_kses_post( pips_pro_activated() ? $this->get_customer_details() : $this->order->get_formatted_billing_address() ); ?>
			<?php if ( 'yes' === get_option( 'pips_display_user_phone', 'no' ) ) : ?>
				<div class="billing-phone"><?php echo wp_kses_post( $this->order->get_billing_phone() ); ?></div>
			<?php endif; ?>
			<?php if ( 'yes' === get_option( 'pips_display_user_email', 'no' ) ) : ?>
				<div class="billing-email"><?php echo wp_kses_post( $this->order->get_billing_email() ); ?></div>
			<?php endif; ?>
		</td>
		<td class="address shipping-address">
			<?php if ( $this->has_shipping_address() ) : ?>
				<h3><?php _e( 'Ship To:', 'sdevs_pips' ); ?></h3>
				<?php echo wp_kses_post( $this->order->has_shipping_address() ? ( pips_pro_activated() ? $this->get_shipping_details() : $this->order->get_formatted_shipping_address() ) : 'N/A' ); ?>
			<?php endif; ?>
		</td>
		<td class="order-data">
			<table>
				<?php if ( 'yes' === get_option( 'pips_display_invoice_number', 'no' ) ) : ?>
					<tr class="invoice-number">
						<th><?php _e( 'Invoice Number:', 'sdevs_pips' ); ?></th>
						<td># <?php echo esc_html( $this->get_invoice_number() ); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( 'yes' === get_option( 'pips_display_invoice_date', 'no' ) ) : ?>
					<tr class="invoice-date">
						<th><?php _e( 'Invoice Date:', 'sdevs_pips' ); ?></th>
						<td><?php echo esc_html( $this->get_invoice_date() ); ?></td>
					</tr>
				<?php endif; ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'sdevs_pips' ); ?></th>
					<td><?php echo esc_html( $this->order->get_id() ); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'sdevs_pips' ); ?></th>
					<td><?php echo esc_html( date( get_option( 'pipspro_invoice_date_format', 'F d, Y' ), strtotime( $this->order->get_date_created() ) ) ); ?></td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Method:', 'sdevs_pips' ); ?></th>
					<td><?php echo wp_kses_post( $this->order->get_payment_method_title() ); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table class="products-table">
	<thead>
		<tr class="product-header">
			<?php foreach ( $this->get_product_invoice_columns()  as $column_key => $column_label ) : ?>
				<th><?php echo esc_html( $column_label ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $this->order->get_items() as $item_id => $item ) : ?>
			<tr class="product-body-row">
				<?php
				foreach ( $this->get_product_invoice_columns() as $column_key => $column_label ) {
					do_action( 'pips_product_column_' . $column_key, $item, $this->order );
				}
				?>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<?php $blank_columns = $this->get_blank_columns(); ?>
		<tr>
			<?php for ( $i = 0; $i < $blank_columns; $i++ ) : ?>
				<td></td>
			<?php endfor; ?>
			<td class="meta-key">
				<strong><?php _e( 'Subtotal', 'sdevs_pips' ); ?> : </strong>
			</td>
			<td class="meta-value">
				<?php echo pips_price( $this->order->get_subtotal(), $this->order->get_currency() ); ?>
			</td>
		</tr>
		<tr>
			<?php for ( $i = 0; $i < $blank_columns; $i++ ) : ?>
				<td></td>
			<?php endfor; ?>
			<td class="meta-key">
				<strong><?php _e( 'Tax', 'sdevs_pips' ); ?> : </strong>
			</td>
			<td class="meta-value">
				<?php echo pips_price( $this->order->get_total_tax(), $this->order->get_currency() ); ?>
			</td>
		</tr>
		<?php if ( $this->order->get_discount_total() != 0 ) : ?>
			<tr>
				<?php for ( $i = 0; $i < $blank_columns; $i++ ) : ?>
					<td></td>
				<?php endfor; ?>
				<td class="meta-key">
					<strong><?php _e( 'Discount', 'sdevs_pips' ); ?> : </strong>
				</td>
				<td class="meta-value">
					- <?php echo wp_kses_post( $this->order->get_discount_to_display() ); ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<?php for ( $i = 0; $i < $blank_columns; $i++ ) : ?>
				<td></td>
			<?php endfor; ?>
			<td class="meta-key">
				<strong><?php _e( 'Total', 'sdevs_pips' ); ?> : </strong>
			</td>
			<td class="meta-value">
				<?php echo pips_price( $this->order->get_total(), $this->order->get_currency() ); ?>
			</td>
		</tr>
	</tfoot>
</table>

<div>
	<div class="notes">
		<?php if ( $this->get_invoice_note() ) : ?>
			<div>
				<h3><?php _e( 'Notes', 'sdevs_pips' ); ?></h3>
				<p><?php echo wp_kses_post( $this->get_invoice_note() ); ?></p>
			</div>
		<?php endif; ?>
		<?php if ( $this->order->get_customer_note() != '' && 'yes' === get_option( 'pips_display_customer_note', 'yes' ) ) : ?>
			<div class="customer-notes">
				<h3><?php _e( 'Customer Notes', 'sdevs_pips' ); ?></h3>
				<p><?php echo wp_kses_post( $this->order->get_customer_note() ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="bottom-spacer"></div>
<?php if ( $this->get_footer_note() ) : ?>
	<div id="footer">
		<?php echo wp_kses_post( $this->get_footer_note() ); ?>
	</div>
<?php endif; ?>

<?php if ( $this->bulk ) : ?>
	<div class="page-break"></div>
<?php endif; ?>
