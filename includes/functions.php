<?php

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

function pips_pro_activated(): bool {
	return class_exists( 'Sdevs_pips_pro_main' );
}

function pips_invoice_template_path() {
	$invoice_template = get_option( 'pips_invoice_template', 'simple' );
	return apply_filters( 'pips_invoice_template_locate', PIPS_PATH . '/templates/' . $invoice_template . '/invoice', $invoice_template );
}

function pips_packing_template_path() {
	$packing_template = get_option( 'pips_packing_template', 'simple' );
	return apply_filters( 'pips_packing_template_locate', PIPS_PATH . '/templates/' . $packing_template . '/packing', $packing_template );
}

function pips_price( $price, $currency ) {
	return wc_price(
		$price,
		array(
			'currency' => $currency,
		)
	);
	return wp_kses_post( get_woocommerce_currency_symbol( $currency ) . ' ' . number_format( $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ) );
}

/**
 * Check if HPOS enabled.
 */
function pips_wc_order_hpos_enabled() {
	return function_exists( 'wc_get_container' ) ?
		wc_get_container()
		->get( CustomOrdersTableController::class )
		->custom_orders_table_usage_is_enabled()
		: false;
}

/**
 * Return a Mpdf instance.
 *
 * @param string $pdf_type invoice or packing.
 *
 * @return Mpdf
 */
function pips_get_generator_instance( $pdf_type = 'invoice' ) {
	$default_config = ( new ConfigVariables() )->getDefaults();
	$font_dirs      = $default_config['fontDir'];

	$default_font_config = ( new FontVariables() )->getDefaults();
	$font_data           = $default_font_config['fontdata'];

	$font_dirs = apply_filters( 'pips_font_dirs', array_merge( $font_dirs, array( PIPS_PATH . '/assets/fonts' ) ) );

	$font_data = apply_filters(
		'pips_font_list',
		array_merge(
			$font_data,
			array(
				'kalpurush' => array(
					'R'          => 'kalpurush.ttf',
					'useOTL'     => 0xFF,
					'useKashida' => 75,
				),
			)
		)
	);

	$default_theme_font = apply_filters( 'pips_default_template_font', 'kalpurush', 'invoice' === $pdf_type ? get_option( 'pips_invoice_template', 'simple' ) : get_option( 'pips_packing_template', 'simple' ) );

	return new Mpdf(
		array(
			'mode'             => 'utf-8',
			'debugfonts'       => false,
			'format'           => ucfirst( get_option( 'pips_invoice_paper_size', 'a4' ) ),
			'fontDir'          => $font_dirs,
			'fontdata'         => $font_data,
			'autoScriptToLang' => true,
			'autoLangToFont'   => true,
			'default_font'     => 'yes' === get_option( 'pipspro_enable_invoice_template_font' ) ? $default_theme_font : get_option( 'pipspro_invoice_font_family', 'kalpurush' ),
		)
	);
}
