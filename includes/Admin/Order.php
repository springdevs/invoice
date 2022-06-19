<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Handle Order related feature
 *
 * Class Order
 * @package SpringDevs\WcPips\Admin
 */
class Order
{
    public function __construct()
    {
        if ('yes' === get_option('pips_order_custom_column', 'yes')) :
            add_filter('manage_edit-shop_order_columns', [$this, 'add_custom_columns']);
            add_action('manage_shop_order_posts_custom_column', [$this, 'add_custom_columns_data'], 10, 2);
        endif;
    }

    public function add_custom_columns($columns)
    {
        $columns['pips_order_column'] = __('Invoice', 'sdevs_pips');
        return $columns;
    }

    public function add_custom_columns_data($column, $post_id)
    {
        if ($column == "pips_order_column") :
            $invoice_view_link = 'admin.php?page=pips_view_pdf&view=pips_invoice&post=' . $post_id;
?>
            <a style="margin-right: 10px;" href="<?php echo esc_html($invoice_view_link); ?>" target="_blank">
                <span class="dashicons dashicons-welcome-view-site"></span>
            </a>
            <a href="<?php echo esc_html($invoice_view_link . '&download=true'); ?>" target="_blank">
                <span class="dashicons dashicons-database-import"></span>
            </a>
<?php
        endif;
    }
}
