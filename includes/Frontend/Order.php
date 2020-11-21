<?php

namespace SpringDevs\Pips\Frontend;

use SpringDevs\Pips\Illuminate\Invoice;

/**
 * Class Order
 * @package SpringDevs\Pips\Frontend
 */
class Order
{
    public function __construct()
    {
        add_action('init', [$this, 'handle_get_requests']);
        add_filter('woocommerce_my_account_my_orders_actions', [$this, 'add_custom_action'], 10, 2);
        add_action('wp_footer', [$this, 'js_scripts']);
    }

    public function handle_get_requests()
    {
        if (is_admin()) return;
        if (isset($_GET['view']) && isset($_GET['post'])) {
            $invoice = new Invoice;
            $invoice->generate_order_pdf();
        }
    }

    public function add_custom_action($actions, $order)
    {
        $invoice_link = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) . '?view=pips_invoice&post=' . $order->get_id();
        if ('download' === get_option('pips_view_invoice_front')) $invoice_link .= "&download=true";
        $actions['pips_invoice'] = array(
            'url'  => $invoice_link,
            'name' => __('Invoice', 'sdevs_wea'),
        );

        return $actions;
    }

    public function js_scripts()
    {
?>
        <script>
            jQuery(document).ready(($) => {
                $("a.pips_invoice").attr('target', '_blank');
            });
        </script>
<?php
    }
}
