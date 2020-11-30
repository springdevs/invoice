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
        if ('yes' === get_option('pips_enable_invoice', 'yes')) :
            $hook = add_submenu_page(null, 'Test PDF', 'Test', 'manage_options', 'pips_view_pdf', function () {
            });
            add_action('load-' . $hook, [$this, 'generate_order_pdf']);
        endif;
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
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $attachment = false;
            if (isset($_GET['download']) && $_GET['download'] === 'true') $attachment = true;
            $dompdf->stream('invoice-' . $this->get_invoice_number(), ['Attachment' => $attachment]);
            exit;
        } elseif (isset($_GET['view']) && isset($_GET['post']) && $_GET['view'] == 'pips_packing_slip') {
            $dompdf = new Dompdf();
            $order = wc_get_order($_GET['post']);
            if (!$order) return;
            $this->order = $order;
            $html = $this->render_template(SDEVS_PIPS_PATH . '/templates/simple/packing.php', ['order' => $order]);
            $dompdf->set_option('chroot', SDEVS_PIPS_PATH);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $attachment = false;
            if (isset($_GET['download']) && $_GET['download'] === 'true') $attachment = true;
            $dompdf->stream('packing-slip-' . $this->get_invoice_number(), ['Attachment' => $attachment]);
            exit;
        }
    }

    public function generate_save_pdf($order_id)
    {
        $dompdf = new Dompdf();
        $order = wc_get_order($order_id);
        if (!$order) return;
        $this->order = $order;
        $html = $this->render_template(SDEVS_PIPS_PATH . '/templates/simple/template.php', ['order' => $order]);
        $dompdf->set_option('chroot', SDEVS_PIPS_PATH);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $attachment = false;
        if (isset($_GET['download']) && $_GET['download'] === 'true') $attachment = true;
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . "/pips";
        if (!file_exists($upload_path)) mkdir($upload_path, 0777, true);
        file_put_contents($upload_path . '/invoice-' . $this->get_invoice_number() . '.pdf', $dompdf->output());
        return $upload_path . '/invoice-' . $this->get_invoice_number() . '.pdf';
    }

    public function render_template($file, $args)
    {
        ob_start();
        if (file_exists($file)) include($file);
        return ob_get_clean();
    }

    public function get_customer_details()
    {
        return apply_filters('pips_get_customer_details', 'N/A', $this->order);
    }

    public function get_shipping_details()
    {
        return apply_filters('pips_get_shipping_details', 'N/A', $this->order);
    }

    public function get_shop_name()
    {
        if (get_option('pips_invoice_shop_name')) return get_option('pips_invoice_shop_name');
        return get_bloginfo('name');
    }

    public function get_shop_address()
    {
        if (get_option('pips_invoice_shop_address')) return get_option('pips_invoice_shop_address');
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
        if (get_option('pips_invoice_note')) return get_option('pips_invoice_note');
        return false;
    }

    public function get_footer_note()
    {
        if (get_option('pips_invoice_footer_note')) return get_option('pips_invoice_footer_note');
        return false;
    }

    public function get_invoice_title()
    {
        return "invoice-" . $this->get_invoice_number();
    }

    public function get_packing_title()
    {
        return "packing-slip-" . $this->get_invoice_number();
    }

    public function has_shipping_address()
    {
        $setting = get_option('pips_invoice_display_shipping_address', 'when_different');
        if ('no' === $setting) return false;
        if ('always' === $setting) return true;
        return $this->order->has_shipping_address();
    }
}
