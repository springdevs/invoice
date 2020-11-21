<?php

namespace SpringDevs\Pips\Admin;

/**
 * Handle Admin MetaBoxes
 *
 * Class MetaBoxes
 * @package SpringDevs\Pips\Admin
 */
class MetaBoxes
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('add_meta_boxes', [$this, "create_meta_boxes"]);
        add_action('save_post_shop_order', [$this, 'save_invoice_meta']);
    }

    public function enqueue_assets()
    {
        wp_enqueue_style('pips_admin_css');
    }

    public function create_meta_boxes()
    {
        // Sidebar [ order action ]
        if ('yes' === get_option('pips_enable_invoice', 'yes')) :
            add_meta_box(
                'pips_order_action',
                'Create PDF',
                [$this, 'order_action_html'],
                'shop_order',
                'side',
                'default'
            );

            // Sidebar [ order edit invoice ]
            add_meta_box(
                'pips_order_edit_invoice',
                'Invoice Details',
                [$this, 'order_edit_invoice'],
                'shop_order',
                'normal',
                'low'
            );
        endif;
    }

    public function order_action_html()
    {
        $invoice_link = 'admin.php?page=pips_view_pdf&view=pips_invoice&post=' . get_the_ID();
?>
        <ul class="pips-action-buttons">
            <li><a href="<?php echo $invoice_link; ?>" class="button exists" alt="PDF Invoice" target="_blank">PDF Invoice</a></li>
            <li><a href="" class="button exists" target="_blank" alt="PDF Packing Slip">PDF Packing Slip</a></li>
        </ul>
    <?php
    }

    public function order_edit_invoice()
    {
        $post_id = get_the_ID();
        $invoice_number = get_post_meta($post_id, '_pips_order_invoice_number', true) ? get_post_meta($post_id, '_pips_order_invoice_number', true) : null;
        $invoice_date = get_post_meta($post_id, '_pips_order_invoice_number', true) ? get_post_meta($post_id, '_pips_order_invoice_date', true) : null;
        if ($invoice_date != null) $invoice_date = date('Y-m-d', $invoice_date);
        $invoice_note = get_post_meta($post_id, '_pips_order_invoice_number', true) ? get_post_meta($post_id, '_pips_order_invoice_note', true) : null;
    ?>
        <table class="form-table">
            <input type="hidden" value="<?php echo wp_create_nonce('pips_order_edit_invoice'); ?>" name="pips_invoice_nonce">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="pips_invoice_number">Invoice Number</label>
                    </th>
                    <td>
                        <input name="pips_invoice_number" type="text" id="pips_invoice_number" placeholder="Custom Invoice Number" class="regular-text" value="<?php echo $invoice_number; ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="pips_invoice_date">Invoice Date</label>
                    </th>
                    <td>
                        <input type="date" name="pips_invoice_date" id="pips_invoice_date" value="<?php echo $invoice_date; ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="pips_invoice_note">Note</label>
                    </th>
                    <td>
                        <textarea type="text" name="pips_invoice_note" id="pips_invoice_note" class="input-text" style="width: 100%;"><?php echo $invoice_note; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
<?php
    }

    public function save_invoice_meta($post_id)
    {
        if (!isset($_POST['pips_invoice_nonce'])) return;
        if (!wp_verify_nonce($_POST['pips_invoice_nonce'], 'pips_order_edit_invoice')) wp_die('Undefined nonce !!');
        $invoice_number = sanitize_text_field($_POST['pips_invoice_number']);
        $invoice_date   = sanitize_text_field($_POST['pips_invoice_date']);
        $invoice_date   = strtotime($invoice_date);
        $invoice_note   = sanitize_text_field($_POST['pips_invoice_note']);
        update_post_meta($post_id, '_pips_order_invoice_number', $invoice_number);
        update_post_meta($post_id, '_pips_order_invoice_date', $invoice_date);
        update_post_meta($post_id, '_pips_order_invoice_note', $invoice_note);
    }
}
