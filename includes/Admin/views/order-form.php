<div class="sdevs_sidebar_form">
	<input type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'pips_order_edit_invoice' ) ); ?>" name="pips_invoice_nonce">
	<p class="form-field sdevs-form">
		<label for="pips_invoice_number">
			<strong><?php _e( 'Invoice Number', 'sdevs_pips' ); ?></strong>
		</label>
		<input name="pips_invoice_number" type="text" id="pips_invoice_number" placeholder="Custom Invoice Number" class="short" value="<?php echo esc_attr( $invoice_number ); ?>">
	</p>
	<p class="form-field sdevs-form">
		<label for="pips_invoice_date">
			<strong><?php _e( 'Invoice Date', 'sdevs_pips' ); ?></strong>
		</label>
		<input style="width: 100%;" type="date" class="short" name="pips_invoice_date" id="pips_invoice_date" value="<?php echo esc_attr( $invoice_date ); ?>" />
	</p>
	<p class="form-field sdevs-form">
		<label for="pips_invoice_note">
			<strong><?php _e( 'Note', 'sdevs_pips' ); ?></strong>
		</label>
		<textarea type="text" name="pips_invoice_note" id="pips_invoice_note" style="width: 100%;"><?php echo esc_html( $invoice_note ); ?></textarea>
	</p>
</div>
