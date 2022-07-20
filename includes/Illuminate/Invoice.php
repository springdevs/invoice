<?php

namespace SpringDevs\WcPips\Illuminate;

use Dompdf\Dompdf;

/**
 * Handle Invoices
 *
 * Class Invoice
 * @package SpringDevs\WcPips\Illuminate
 */
class Invoice
{
    public $order;
    public $bulk = false;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('pips_invoice_template_html_header', [$this, 'load_stylesheets_invoice']);
        add_action('pips_packing_template_html_header', [$this, 'load_stylesheets_packing']);
        add_action('pips_product_column_product', [$this, 'product_content']);
        add_action('pips_product_column_qty', [$this, 'product_qty']);
        add_action('pips_product_column_subtotal', [$this, 'product_subtotal']);
    }

    public function product_content($item)
    {
        $product_variation_id = $item['variation_id'];
        // Check if product has variation.
        if ($product_variation_id) {
            $product = wc_get_product($item['variation_id']);
        } else {
            $product = wc_get_product($item['product_id']);
        }
?>
        <td class="product-content">
            <span class="item-name"><?php echo esc_html($item->get_name()); ?></span>
            <?php if ($this->get_product_sku($product)) : ?>
                <dl class="meta"><small>SKU: <?php echo esc_html($this->get_product_sku($product)); ?></small></dl>
            <?php endif; ?>
        </td>
    <?php
    }

    public function product_qty($item)
    {
    ?>
        <td>
            <?php echo esc_html($item->get_quantity()); ?>
        </td>
    <?php
    }

    public function product_subtotal($item)
    {
        $product_variation_id = $item['variation_id'];
        // Check if product has variation.
        if ($product_variation_id) {
            $product = wc_get_product($item['variation_id']);
        } else {
            $product = wc_get_product($item['product_id']);
        }
    ?>
        <td>
            <?php echo wp_kses_post(apply_filters('woocommerce_get_price_html', $this->get_line_subtotal($this->order, $item), $product)); ?>
        </td>
    <?php
    }

    public function load_stylesheets_invoice()
    {
    ?>
        <link rel="stylesheet" href="<?php echo esc_attr(pips_invoice_template_path() . '/style.css'); ?>" />
        <?php if (!pips_pro_activated()) : ?>
            <style>
                body {
                    font-family: 'Open Sans', sans-serif;
                    font-size: 9pt;
                }
            </style>
        <?php endif; ?>
    <?php
    }

    public function load_stylesheets_packing()
    {
    ?>
        <link rel="stylesheet" href="<?php echo esc_attr(pips_packing_template_path() . '/style.css'); ?>" />
<?php
    }

    public function admin_menu()
    {
        if ('yes' === get_option('pips_enable_invoice', 'yes')) :
            $hook = add_submenu_page(null, 'Preview PDF', 'Preview PDF', 'manage_options', 'pips_view_pdf', function () {
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
            $invoice_template_path = pips_invoice_template_path();
            $html = $this->render_template($invoice_template_path . '/header.php', []);
            $html .= $this->render_template($invoice_template_path . '/template.php', ['order' => $order]);
            $html .= $this->render_template($invoice_template_path . '/footer.php', []);
            $options = $dompdf->getOptions();
            $options->set('chroot', PIPS_PATH);
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper(get_option("pips_invoice_paper_size", "a4"), 'portrait');
            $dompdf->render();
            $attachment = false;
            if (isset($_GET['download']) && $_GET['download'] === 'true') $attachment = true;
            $dompdf->stream('invoice-' . $this->get_invoice_number(), ['Attachment' => $attachment]);
            exit;
        } elseif (isset($_GET['view']) && isset($_GET['post']) && $_GET['view'] == 'pips_packing_slip' && "yes" === get_option("pips_enable_packing_slip", "yes")) {
            $dompdf = new Dompdf();
            $order = wc_get_order($_GET['post']);
            if (!$order) return;
            $this->order = $order;
            $packing_template_path = pips_packing_template_path();
            $html = $this->render_template($packing_template_path . '/header.php', []);
            $html .= $this->render_template($packing_template_path . '/template.php', ['order' => $order]);
            $html .= $this->render_template($packing_template_path . '/footer.php', []);
            $options = $dompdf->getOptions();
            $options->set('chroot', PIPS_PATH);
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $attachment = false;
            if (isset($_GET['download']) && $_GET['download'] === 'true') $attachment = true;
            $dompdf->stream('packing-slip-' . $this->get_invoice_number(), ['Attachment' => $attachment]);
            exit;
        }

        do_action('pips_pdf_generator');
    }

    public function generate_save_pdf($order_id)
    {
        $dompdf = new Dompdf();
        $order = wc_get_order($order_id);
        if (!$order) return;
        $this->order = $order;
        $html = $this->render_template(PIPS_PATH . '/templates/simple/invoice/header.php', []);
        $html .= $this->render_template(PIPS_PATH . '/templates/simple/invoice/template.php', ['order' => $order]);
        $html .= $this->render_template(PIPS_PATH . '/templates/simple/invoice/footer.php', []);
        $options = $dompdf->getOptions();
        $options->set('chroot', PIPS_PATH);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
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
        if ($order_meta) return date(get_option('pipspro_invoice_date_format', 'F d, Y'), $order_meta);
        $default_date = date(get_option('pipspro_invoice_date_format', 'F d, Y'), strtotime($this->order->get_date_created()));
        return apply_filters("pips_filter_invoice_date", $default_date, $this->order->get_id());
    }

    public function get_product_invoice_columns()
    {
        $columns = [
            "product" => __("Product", "sdevs_pips"),
            "qty" => __("Qty", "sdevs_pips"),
            "subtotal" => __("Subtotal", "sdevs_pips")
        ];

        return apply_filters('pips_invoice_product_columns', $columns, $this->order);
    }

    public function get_blank_columns()
    {
        return count($this->get_product_invoice_columns()) - 2;
    }

    public function get_invoice_note()
    {
        $order_meta = get_post_meta($this->order->get_id(), '_pips_order_invoice_note', true);
        if ($order_meta) return $order_meta;
        if (get_option('pips_invoice_note')) return get_option('pips_invoice_note');
        return false;
    }

    public function get_packing_note()
    {
        $order_meta = get_post_meta($this->order->get_id(), '_pips_order_packing_note', true);
        if ($order_meta) return $order_meta;
        if (get_option('pips_packing_slip_note')) return get_option('pips_packing_slip_note');
        return false;
    }

    public function get_footer_note()
    {
        if (get_option('pips_invoice_footer_note')) return get_option('pips_invoice_footer_note');
        return false;
    }

    public function get_invoice_title(): string
    {
        if ($this->bulk) return "Bulk PDF invoices";
        return "invoice-" . $this->get_invoice_number();
    }

    public function get_packing_title(): string
    {
        if ($this->bulk) return "Bulk PDF packing slips";
        return "packing-slip-" . $this->get_invoice_number();
    }

    public function has_shipping_address(): bool
    {
        $setting = get_option('pips_invoice_display_shipping_address', 'when_different');
        if ('no' === $setting) return false;
        if ('always' === $setting) return true;
        return $this->order->has_shipping_address() && $this->order->get_billing_address_1() != $this->order->get_shipping_address_1();
    }

    public function get_product_sku($product)
    {
        $sku = $product->get_sku();
        return apply_filters("pips_product_sku", $sku, $product);
    }

    public function get_line_subtotal($order, $item)
    {
        $single_price = $order->get_item_subtotal($item, false, true);
        $regular_price = $single_price * $item->get_quantity();
        $sale_price = $item->get_total();
        $currency_code = $order->get_currency();
        if ($regular_price != $sale_price && "yes" === get_option('pipspro_invoice_slashed_price', 'yes')) return wc_format_sale_price($regular_price, $sale_price);
        return pips_price($sale_price, $currency_code);
    }
}
