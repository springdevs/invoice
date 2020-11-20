<?php

namespace SpringDevs\Pips\Illuminate;

use Dompdf\Dompdf;

/**
 * Handle Invoices
 *
 * Class Invoice
 * @package SpringDevs\Pips\Illuminate
 */
class Invoice
{
    protected $order;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    public function admin_menu()
    {
        $hook = add_submenu_page(null, 'Test PDF', 'Test', 'manage_options', 'pips_view_pdf', function () {
        });
        add_action('load-' . $hook, [$this, 'generate_order_pdf']);
    }

    public function generate_order_pdf()
    {
        if (isset($_GET['view']) && isset($_GET['post']) && $_GET['view'] == 'pips_invoice') {
            $dompdf = new Dompdf();
            $order = wc_get_order($_GET['post']);
            if (!$order) return;
            $this->order = $order;
            $html = $this->render_template(SDEVS_PIPS_PATH . '/templates/simple/template.php', ['order' => $order]);
            $dompdf->set_option('chroot', SDEVS_PIPS_PATH);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('invoice-' . $this->get_invoice_number(), ['Attachment' => false]);
            exit;
        }
    }

    public function render_template($file, $args)
    {
        ob_start();
        if (file_exists($file)) include($file);
        return ob_get_clean();
    }

    public function has_header_logo()
    {
        return false;
    }

    public function get_invoice_number()
    {
        $order_meta = get_post_meta($this->order->get_id(), '_pips_order_invoice_number', true);
        if ($order_meta) return $order_meta;
        return $this->order->get_id();
    }

    public function get_invoice_date()
    {
        $order_meta = get_post_meta($this->order->get_id(), '_pips_order_invoice_date', true);
        if ($order_meta) return date("F d,Y", $order_meta);
        return date("F d,Y", strtotime($this->order->get_date_created()));
    }

    public function get_invoice_note()
    {
        $order_meta = get_post_meta($this->order->get_id(), '_pips_order_invoice_note', true);
        if ($order_meta) return $order_meta;
        return false;
    }

    public function get_invoice_title()
    {
        return "invoice-" . $this->get_invoice_number();
    }
}
