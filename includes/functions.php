<?php

function pips_pro_activated(): bool
{
    return class_exists('Sdevs_pips_pro_main');
}

function pips_invoice_template_path()
{
    $invoice_template = get_option('pips_invoice_template', 'simple');
    return apply_filters('pips_invoice_template_locate', PIPS_PATH . '/templates/' . $invoice_template . '/invoice', $invoice_template);
}

function pips_packing_template_path()
{
    $packing_template = get_option('pips_packing_template', 'simple');
    return apply_filters('pips_packing_template_locate', PIPS_PATH . '/templates/' . $packing_template . '/packing', $packing_template);
}

function pips_price($price, $currency)
{
    return wp_kses_post(get_woocommerce_currency_symbol($currency) . " " . number_format($price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()));
}
