<table class="head container">
    <tr>
        <td class="header">
            <?php if (get_option('pips_invoice_logo_height')) : ?>
                <img height="<?php echo get_option('pips_invoice_logo_height'); ?>" src="<?php echo get_option('pips_invoice_logo'); ?>" alt="Logo" />
            <?php else : ?>
                <img src="<?php echo get_option('pips_invoice_logo'); ?>" alt="Logo" />
            <?php endif; ?>
        </td>
        <td class="shop-info">
            <div class="shop-name">
                <h3><?php echo $this->get_shop_name(); ?></h3>
                <?php if ($this->get_shop_address()) : ?>
                    <p><?php echo $this->get_shop_address(); ?></p>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>

<table class="order-data-addresses">
    <tr>
        <td>
            <h1><?php _e('PACKING SLIP', 'sdevs_wea'); ?></h1>
        </td>
    </tr>
    <tr>
        <td class="address billing-address">
            <?php echo $this->order->has_shipping_address() ? $this->order->get_formatted_shipping_address() : $this->order->get_formatted_billing_address(); ?>
            <?php if ('yes' === get_option('pips_packing_slip_display_email', 'no')) : ?>
                <div class="billing-email"><?php echo $this->order->get_billing_email(); ?></div>
            <?php endif; ?>
            <?php if ('yes' === get_option('pips_packing_slip_display_phone', 'no')) : ?>
                <div class="billing-phone"><?php echo $this->order->get_billing_phone(); ?></div>
            <?php endif; ?>
        </td>
        <td></td>
        <td class="order-data">
            <table>
                <tr class="order-number">
                    <th><?php _e('Order Number:', 'sdevs_wea'); ?></th>
                    <td><?php echo $this->order->get_id(); ?></td>
                </tr>
                <tr class="order-date">
                    <th><?php _e('Order Date:', 'sdevs_wea'); ?></th>
                    <td><?php echo date("F d,Y", strtotime($this->order->get_date_created())); ?></td>
                </tr>
                <tr class="payment-method">
                    <th><?php _e('Shipping Method:', 'sdevs_wea'); ?></th>
                    <td><?php echo $this->order->get_shipping_method(); ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="order-details">
    <thead>
        <tr>
            <th class="product"><?php _e('Product', 'sdevs_wea'); ?></th>
            <th class="quantity"><?php _e('Quantity', 'sdevs_wea'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->order->get_items() as $item_id => $item) : ?>
            <?php
            $product_variation_id = $item['variation_id'];
            // Check if product has variation.
            if ($product_variation_id) {
                $product = wc_get_product($item['variation_id']);
            } else {
                $product = wc_get_product($item['product_id']);
            }
            $sku = $product->get_sku();
            ?>
            <tr class="wpo_wcpdf_item_row_class">
                <td class="product">
                    <span class="item-name"><?php echo $item->get_name(); ?></span>
                    <dl class="meta"><small>SKU: <?php echo $sku; ?></small></dl>
                </td>
                <td class="quantity"><?php echo $item->get_quantity(); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>
    <div style="margin: 40px 0;">
        <?php if ($this->get_packing_note() && "yes" === get_option("pips_packing_slip_display_note", "no")) : ?>
            <div>
                <h3><?php _e('Notes', 'sdevs_wea'); ?></h3>
                <p><?php echo $this->get_packing_note(); ?></p>
            </div>
        <?php endif; ?>
        <?php if ($this->order->get_customer_note() != '' && 'yes' === get_option('pips_display_customer_note', 'yes')) : ?>
            <div class="customer-notes">
                <h3><?php _e('Customer Notes', 'sdevs_wea'); ?></h3>
                <p><?php echo $this->order->get_customer_note(); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="bottom-spacer"></div>
<?php if ($this->get_footer_note()) : ?>
    <div id="footer">
        <?php echo $this->get_footer_note(); ?>
    </div>
<?php endif; ?>

<?php if ($this->bulk) : ?>
    <div style="page-break-after: always;"></div>
<?php endif; ?>