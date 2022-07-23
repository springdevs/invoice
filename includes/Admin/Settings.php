<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Settings class
 * Woocommerce Settings Tabs
 */
class Settings {

	public function __construct() {
		add_filter( 'woocommerce_get_sections_pips', array( $this, 'add_section' ), 10 );
		add_filter( 'woocommerce_get_settings_pips', array( $this, 'settings_content' ) );
	}

	public function add_section( $sections ) {
		$sections['']          = __( 'Invoices', 'sdevs_pips' );
		$sections['pips_slip'] = __( 'Packing Slips', 'sdevs_pips' );
		return $sections;
	}

	public function settings_content( $settings ) {
		global $current_section;
		if ( $current_section == '' ) {
			return $this->invoice_settings();
		} elseif ( $current_section == 'pips_slip' ) {
			return $this->packing_slip_settings();
		}
		return $settings;
	}

	public function invoice_settings() {
		$invoice_settings = array();

		$invoice_settings[] = array(
			'name' => __( 'Invoice Settings', 'sdevs_pips' ),
			'type' => 'title',
			'desc' => __( 'The following options are used to configure invoice settings', 'sdevs_pips' ),
			'id'   => 'invoice',
		);

		// Enable / Disable Invoice
		$invoice_settings[] = array(
			'name'    => __( 'Enable Invoice', 'sdevs_pips' ),
			'id'      => 'pips_enable_invoice',
			'type'    => 'checkbox',
			'default' => 'yes',
			'desc'    => __( 'Enable or Disable invoice feature', 'sdevs_pips' ),
		);

		// attach pdf [ new order ]
		$invoice_settings[] = array(
			'name'          => __( 'Attach to', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_new_order_admin',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'default'       => 'yes',
			'desc'          => __( 'New order (Admin email)', 'sdevs_pips' ),
		);

		// attach pdf [ Cancelled order ]
		$invoice_settings[] = array(
			'name'          => __( 'Cancelled order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_cancelled_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Cancelled order', 'sdevs_pips' ),
		);

		// attach pdf [ Failed order ]
		$invoice_settings[] = array(
			'name'          => __( 'Failed order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_failed_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Failed order', 'sdevs_pips' ),
		);

		// attach pdf [ Order on-hold ]
		$invoice_settings[] = array(
			'name'          => __( 'Order on-hold', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_on-hold_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Order on-hold', 'sdevs_pips' ),
		);

		// attach pdf [ Processing order ]
		$invoice_settings[] = array(
			'name'          => __( 'Processing order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_processing_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Processing order', 'sdevs_pips' ),
		);

		// attach pdf [ Completed order ]
		$invoice_settings[] = array(
			'name'          => __( 'Completed order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_completed_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Completed order', 'sdevs_pips' ),
		);

		// attach pdf [ Refunded order ]
		$invoice_settings[] = array(
			'name'          => __( 'Refunded order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_refunded_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Refunded order', 'sdevs_pips' ),
		);

		// attach pdf [ Customer note ]
		$invoice_settings[] = array(
			'name'          => __( 'Customer note', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_admin_note_order',
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'default'       => 'yes',
			'desc'          => __( 'Customer note', 'sdevs_pips' ),
		);

		// Disable for:
		$invoice_settings[] = array(
			'title'   => __( 'Disable for', 'sdevs_pips' ),
			'id'      => 'pips_invoice_disable_statuses',
			'class'   => 'wc-enhanced-select',
			'type'    => 'multiselect',
			'options' => function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array(),
		);

		// Paper Size
		$invoice_settings[] = array(
			'title'   => __( 'Paper Size', 'sdevs_pips' ),
			'id'      => 'pips_invoice_paper_size',
			'type'    => 'select',
			'options' => array(
				'a4'     => __( 'A4', 'sdevs_pips' ),
				'letter' => __( 'Letter', 'sdevs_pips' ),
			),
		);

		// Display shipping address
		$invoice_settings[] = array(
			'name'    => __( 'Display shipping address', 'sdevs_pips' ),
			'id'      => 'pips_invoice_display_shipping_address',
			'type'    => 'select',
			'options' => array(
				'no'             => __( 'No', 'sdevs_pips' ),
				'when_different' => __( 'Only when different from billing address', 'sdevs_pips' ),
				'always'         => __( 'Always', 'sdevs_pips' ),
			),
			'default' => 'when_different',
		);

		// display invoice number
		$invoice_settings[] = array(
			'name'          => __( 'Show / Hide', 'sdevs_pips' ),
			'id'            => 'pips_display_invoice_number',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'desc'          => __( 'Invoice Number', 'sdevs_pips' ),
		);

		// Enable invoice date
		$invoice_settings[] = array(
			'name'          => __( 'Invoice Date', 'sdevs_pips' ),
			'id'            => 'pips_display_invoice_date',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Invoice Date', 'sdevs_pips' ),
		);

		// display email address
		$invoice_settings[] = array(
			'name'          => __( 'Email address', 'sdevs_pips' ),
			'id'            => 'pips_display_user_email',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Email address', 'sdevs_pips' ),
		);

		// Display phone number
		$invoice_settings[] = array(
			'name'          => __( 'Phone number', 'sdevs_pips' ),
			'id'            => 'pips_display_user_phone',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Phone number', 'sdevs_pips' ),
		);

		// Display customer note
		$invoice_settings[] = array(
			'name'          => __( 'Display customer note', 'sdevs_pips' ),
			'id'            => 'pips_display_customer_note',
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'default'       => 'yes',
			'desc'          => __( 'Display customer note', 'sdevs_pips' ),
		);

		// custom colum
		$invoice_settings[] = array(
			'name'    => __( 'Custom Column', 'sdevs_pips' ),
			'id'      => 'pips_order_custom_column',
			'type'    => 'checkbox',
			'default' => 'yes',
			'desc'    => __( 'Order Column for direct view or download pdf (Admin)', 'sdevs_pips' ),
		);

		// Disable for free orders
		$invoice_settings[] = array(
			'name' => __( 'Disable for free orders', 'sdevs_pips' ),
			'id'   => 'pips_free_order_invoice',
			'type' => 'checkbox',
			'desc' => __( 'Disable invoice when the order total is ' . wc_price( 0 ), 'sdevs_pips' ),
		);

		// view pdf
		$invoice_settings[] = array(
			'name'     => __( 'view PDF', 'sdevs_pips' ),
			'id'       => 'pips_view_invoice_front',
			'type'     => 'select',
			'options'  => array(
				'download' => __( 'Download the PDF', 'sdevs_pips' ),
				'display'  => __( 'Open the PDF in a new browser tab/window', 'sdevs_pips' ),
			),
			'default'  => 'display',
			'desc'     => __( 'How do you want to view the PDF ?', 'sdevs_pips' ),
			'desc_tip' => true,
		);

		if ( ! pips_pro_activated() ) {
			$available_templates = array(
				'simple' => __( 'Simple', 'sdevs_pips' ),
			);
			$available_templates = apply_filters( 'pips_invoice_templates', $available_templates );
			$invoice_settings[]  = array(
				'name'    => __( 'Template', 'sdevs_pips' ),
				'id'      => 'pips_invoice_template',
				'type'    => 'select',
				'default' => 'simple',
				'options' => $available_templates,
				'desc'    => sprintf( '%s <a href="https://springdevs.com/invoice-templates" target="_blank">%s</a>', __( 'To quick preview all of our premium templates. please go', 'sdevs_pips' ), __( 'here', 'sdevs_pips' ) ),
			);
		}

		// Display invoice button on MyAccount Orders
		$invoice_settings[] = array(
			'name'    => __( 'Display invoice button (Front)', 'sdevs_pips' ),
			'id'      => 'pips_display_invoice_btn_front',
			'type'    => 'select',
			'options' => array(
				'always'          => __( 'Always', 'sdevs_pips' ),
				'order_status_pc' => __( 'When order status is processing/completed', 'sdevs_pips' ),
				'never'           => __( 'Never', 'sdevs_pips' ),
			),
			'default' => 'always',
		);

		// Logo Upload
		$invoice_settings[] = array(
			'name' => __( 'Invoice Logo', 'sdevs_pips' ),
			'id'   => 'pips_invoice_logo',
			'type' => 'text',
			'desc' => __( 'Logo URL for invoice', 'sdevs_pips' ),
		);

		// Logo height
		$invoice_settings[] = array(
			'name'        => __( 'Logo height', 'sdevs_pips' ),
			'id'          => 'pips_invoice_logo_height',
			'type'        => 'text',
			'placeholder' => '3cm',
			'css'         => 'width: 6em;',
		);

		// Shop Name
		$invoice_settings[] = array(
			'name'        => __( 'Shop Name', 'sdevs_pips' ),
			'id'          => 'pips_invoice_shop_name',
			'type'        => 'text',
			'placeholder' => 'Enter Shop Name',
		);

		// Shop Address
		$invoice_settings[] = array(
			'name' => __( 'Shop Address', 'sdevs_pips' ),
			'id'   => 'pips_invoice_shop_address',
			'type' => 'textarea',
		);

		// Default Invoice Note
		$invoice_settings[] = array(
			'name' => __( 'Default Invoice Note', 'sdevs_pips' ),
			'id'   => 'pips_invoice_note',
			'type' => 'textarea',
		);

		// Footer
		$invoice_settings[] = array(
			'name' => __( 'Footer', 'sdevs_pips' ),
			'id'   => 'pips_invoice_footer_note',
			'type' => 'textarea',
		);

		$invoice_settings[] = array(
			'type' => 'sectionend',
			'id'   => 'invoice',
		);
		return $invoice_settings;
	}

	public function packing_slip_settings(): array {
		$templates = array(
			'simple' => __( 'Simple', 'sdevs_pips_pro' ),
		);
		$templates = apply_filters( 'pips_packing_templates', $templates );
		$fields    = array(
			array(
				'name' => __( 'Packing Slips Settings', 'sdevs_pips' ),
				'type' => 'title',
				'desc' => __( 'The following options are used to configure packing slips', 'sdevs_pips' ),
				'id'   => 'packing-slip',
			),
			array(
				'name'    => __( 'Enable Packing Slips', 'sdevs_pips' ),
				'id'      => 'pips_enable_packing_slip',
				'type'    => 'checkbox',
				'default' => 'yes',
				'desc'    => __( 'Enable or Disable packing slips feature', 'sdevs_pips' ),
			),
			array(
				'name'    => __( 'Template', 'sdevs_pips' ),
				'id'      => 'pips_packing_template',
				'type'    => 'select',
				'default' => 'simple',
				'options' => $templates,
				'desc'    => sprintf( '%s <a href="https://springdevs.com/packing-templates" target="_blank">%s</a>', __( 'To quick preview all of our premium templates. please go', 'sdevs_pips' ), __( 'here', 'sdevs_pips' ) ),
			),
			array(
				'name' => __( 'Display email address', 'sdevs_pips' ),
				'id'   => 'pips_packing_slip_display_email',
				'type' => 'checkbox',
			),
			array(
				'name' => __( 'Display phone number', 'sdevs_pips' ),
				'id'   => 'pips_packing_slip_display_phone',
				'type' => 'checkbox',
			),
			array(
				'name' => __( 'Display note', 'sdevs_pips' ),
				'id'   => 'pips_packing_slip_display_note',
				'type' => 'checkbox',
			),
			array(
				'name' => __( 'Default Note', 'sdevs_pips' ),
				'id'   => 'pips_packing_slip_note',
				'type' => 'textarea',
			),
			array(
				'name' => __( 'Display customer notes', 'sdevs_pips' ),
				'id'   => 'pips_packing_slip_display_customer_note',
				'type' => 'checkbox',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'packing-slip',
			),
		);
		return $fields;
	}
}
