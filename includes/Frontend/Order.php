<?php

namespace SpringDevs\WcPips\Frontend;

use SpringDevs\WcPips\Illuminate\Invoice;

/**
 * Class Order
 * @package SpringDevs\WcPips\Frontend
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
            $order = wc_get_order((int)$_GET['post']);
            if ($this->check_if_invoice_front_allowed($order)) {
                $invoice = new Invoice;
                $invoice->generate_order_pdf();
            }
        }
    }

    public function check_if_invoice_front_allowed($order)
    {
        return (get_option('pips_display_invoice_btn_front', 'always') === 'always') || (get_option('pips_display_invoice_btn_front', 'always') === 'order_status_pc' && in_array($order->get_status(), ['processing', 'completed']));
    }

    public function add_custom_action($actions, $order)
    {
        if ($this->check_if_invoice_front_allowed($order)) {
            $actions = $this->invoice_button($actions, $order);
        }

        return $actions;
    }

    public function invoice_button($actions, $order)
    {
        $invoice_link = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) . '?view=pips_invoice&post=' . $order->get_id();
        if ('download' === get_option('pips_view_invoice_front')) $invoice_link .= "&download=true";
        $actions['pips_invoice'] = array(
            'url'  => $invoice_link,
            'name' => __('Invoice', 'sdevs_pips'),
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
