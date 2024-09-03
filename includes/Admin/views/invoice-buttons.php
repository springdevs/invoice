<ul class="pips-action-buttons">
	<?php if ( 'yes' === get_option( 'pips_enable_invoice', 'yes' ) ) : ?>
		<li><a href="<?php echo esc_html( $invoice_link ); ?>" class="button button-small" alt="PDF Invoice" target="_blank"><?php esc_html_e( 'Generate Invoice', 'sdevs_pips' ); ?></a></li>
	<?php endif; ?>
	<?php if ( 'yes' === get_option( 'pips_enable_packing_slip', 'yes' ) ) : ?>
		<li><a href="<?php echo esc_html( $packing_link ); ?>" class="button button-small" target="_blank" alt="PDF Packing Slip"><?php esc_html_e( 'Generate Packing Slip', 'sdevs_pips' ); ?></a></li>
	<?php endif; ?>
</ul>
