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
        // Sidebar [ pdf buttons & forms ]
        if ('yes' === get_option('pips_enable_invoice', 'yes')) :
            add_meta_box(
                'pips_order_action',
                __('Order Invoices', 'sdevs_wea'),
                [$this, 'order_action_html'],
                'shop_order',
                'side',
                'default'
            );
        endif;
    }

    public function order_action_html()
    {
        $post_id = get_the_ID();
        $invoice_number = get_post_meta($post_id, '_pips_order_invoice_number', true) ? get_post_meta($post_id, '_pips_order_invoice_number', true) : null;
        $invoice_date = get_post_meta($post_id, '_pips_order_invoice_number', true) ? get_post_meta($post_id, '_pips_order_invoice_date', true) : null;
        if ($invoice_date != null) $invoice_date = date('Y-m-d', $invoice_date);
        $invoice_note = get_post_meta($post_id, '_pips_order_invoice_number', true) ? get_post_meta($post_id, '_pips_order_invoice_note', true) : null;

        $invoice_link = 'admin.php?page=pips_view_pdf&view=pips_invoice&post=' . $post_id;
        $packing_link = 'admin.php?page=pips_view_pdf&view=pips_packing_slip&post=' . $post_id;
        if (sdevs_is_pro_module_activate('pdf-invoices-and-packing-slips-pro')) {
            do_action('pipspro_load_order_action_html', $post_id, $invoice_link, $packing_link);
        } else {
            include_once 'views/invoice-buttons.php';
        }
        include_once 'views/order-form.php';
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
