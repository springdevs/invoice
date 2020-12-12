<ul class="pips-action-buttons">
    <li><a href="<?php echo $invoice_link; ?>" class="button exists" alt="PDF Invoice" target="_blank"><?php _e('PDF Invoice', 'sdevs_wea'); ?></a></li>
    <?php if ("yes" === get_option("pips_enable_packing_slip", "yes")) : ?>
        <li><a href="<?php echo $packing_link; ?>" class="button exists" target="_blank" alt="PDF Packing Slip"><?php _e('PDF Packing Slip', 'sdevs_wea'); ?></a></li>
    <?php endif; ?>
</ul>