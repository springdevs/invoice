<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo esc_html( $this->get_invoice_title() ); ?></title>
	<?php if ( ! pips_pro_activated() || get_option( 'pipspro_enable_invoice_template_font', 'yes' ) === 'yes' ) : ?>
		<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
		<style>
			body {
				font-family: 'Noto Sans', sans-serif;
				font-size: 9pt;
			}
		</style>
	<?php endif; ?>
	<?php do_action( 'pips_invoice_template_html_header' ); ?>
</head>

<body>