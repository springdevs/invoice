<ul class="pips-action-buttons">
    <li><a href="<?php echo $invoice_link; ?>" class="button exists" alt="PDF Invoice" target="_blank"><?php _e('PDF Invoice', 'sdevs_pips'); ?></a></li>
    <?php if ("yes" === get_option("pips_enable_packing_slip", "yes")) : ?>
        <li><a href="<?php echo $packing_link; ?>" class="button exists" target="_blank" alt="PDF Packing Slip"><?php _e('PDF Packing Slip', 'sdevs_pips'); ?></a></li>
    <?php endif; ?>
</ul>