<?php

namespace SpringDevs\WcPips\Admin;

/**
 * Settings class
 * Woocommerce Settings Tabs
 */
class Settings {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_pips', array( $this, 'add_section' ), 10 );
		add_filter( 'woocommerce_get_settings_pips', array( $this, 'settings_content' ) );
		add_action( 'woocommerce_update_option', array( $this, 'reset_sequential' ) );
	}

	/**
	 * Reset sequential before new start_from saved.
	 *
	 * @return void
	 */
	public function reset_sequential() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['pips_invoice_number_start'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$new_value = (int) sanitize_text_field( wp_unslash( $_POST['pips_invoice_number_start'] ) );
		$old_value = get_option( 'pips_invoice_number_start', 1 );

		if ( $new_value !== $old_value ) {
			delete_option( 'pips_last_invoice_number' );
		}
	}

	/**
	 * Add settings section.
	 *
	 * @param array $sections Sections.
	 *
	 * @return array
	 */
	public function add_section( $sections ) {
		$sections['']              = __( 'Invoices', 'sdevs_pips' );
		$sections['packing_slips'] = __( 'Packing Slips', 'sdevs_pips' );
		return $sections;
	}

	/**
	 * Settings content.
	 *
	 * @param array $settings tabs.
	 *
	 * @return array
	 */
	public function settings_content( $settings ) {
		global $current_section;
		if ( '' === $current_section ) {
			return $this->invoice_settings();
		} elseif ( 'packing_slips' === $current_section ) {
			return $this->packing_slip_settings();
		}
		return $settings;
	}

	/**
	 * Invoice settings.
	 *
	 * @return array
	 */
	public function invoice_settings() {
		wp_enqueue_media();
		wp_enqueue_script( 'pips_settings' );

		$invoice_settings = array();

		$invoice_settings[] = array(
			'name' => __( 'Invoice Settings', 'sdevs_pips' ),
			'type' => 'title',
			'desc' => __( 'The following options are used to configure invoice settings', 'sdevs_pips' ),
			'id'   => 'invoice',
		);

		$invoice_settings[] = array(
			'name'    => __( 'Enable Invoice', 'sdevs_pips' ),
			'id'      => 'pips_enable_invoice',
			'type'    => 'checkbox',
			'default' => 'yes',
			'desc'    => __( 'Enable or Disable invoice feature', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Attach to', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_new_order_admin',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'default'       => 'yes',
			'desc'          => __( 'New order (Admin email)', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Cancelled order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_cancelled_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Cancelled order', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Failed order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_failed_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Failed order', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Order on-hold', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_on-hold_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Order on-hold', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Processing order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_processing_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Processing order', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Completed order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_completed_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Completed order', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Refunded order', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_refunded_order',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Refunded order', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Customer note', 'sdevs_pips' ),
			'id'            => 'pips_attach_pdf_admin_note_order',
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'default'       => 'yes',
			'desc'          => __( 'Customer note', 'sdevs_pips' ),
		);

		// phpcs:ignore
		// $invoice_settings[] = array(
		// 'title'   => __( 'Disable for', 'sdevs_pips' ),
		// 'id'      => 'pips_invoice_disable_statuses',
		// 'class'   => 'wc-enhanced-select',
		// 'type'    => 'multiselect',
		// 'options' => function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array(),
		// );

		$invoice_settings[] = array(
			'title'   => __( 'Paper Size', 'sdevs_pips' ),
			'id'      => 'pips_invoice_paper_size',
			'type'    => 'select',
			'options' => array(
				'a4'     => __( 'A4', 'sdevs_pips' ),
				'letter' => __( 'Letter', 'sdevs_pips' ),
			),
		);

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

		$invoice_settings[] = array(
			'name'          => __( 'Show / Hide', 'sdevs_pips' ),
			'id'            => 'pips_display_invoice_number',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'desc'          => __( 'Invoice Number', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Invoice Date', 'sdevs_pips' ),
			'id'            => 'pips_display_invoice_date',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Invoice Date', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Email address', 'sdevs_pips' ),
			'id'            => 'pips_display_user_email',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Email address', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Phone number', 'sdevs_pips' ),
			'id'            => 'pips_display_user_phone',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'desc'          => __( 'Phone number', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'          => __( 'Display customer note', 'sdevs_pips' ),
			'id'            => 'pips_display_customer_note',
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'default'       => 'yes',
			'desc'          => __( 'Display customer note', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name'              => __( 'Invoice Number', 'sdevs_pips' ),
			'id'                => 'pips_invoice_number_type',
			'type'              => 'select',
			'default'           => 'order_id',
			'options'           => array(
				'order_id'   => __( 'Using Order Id', 'sdevs_pips' ),
				'sequential' => __( 'Sequential numbering', 'sdevs_pips' ),
			),
			'custom_attributes' => array(
				'required' => true,
			),
		);

		$invoice_settings[] = array(
			'name'              => __( 'Invoice Number Start from', 'sdevs_pips' ),
			'id'                => 'pips_invoice_number_start',
			'row_class'         => 'pips_invoice_number_start',
			'type'              => 'number',
			'default'           => 1,
			'custom_attributes' => array(
				'required' => true,
				'min'      => 1,
			),
		);

		$invoice_settings[] = array(
			'name'              => __( 'Invoice Number Format', 'sdevs_pips' ),
			'id'                => 'pips_invoice_number_format',
			'type'              => 'text',
			'default'           => '[number]',
			'custom_attributes' => array(
				'required' => true,
			),
			'desc'              => '<code>[number]</code> is the placeholder to display invoice number',
		);

		$invoice_settings[] = array(
			'name'              => __( 'Invoice File name Format', 'sdevs_pips' ),
			'id'                => 'pips_invoice_file_name_format',
			'type'              => 'text',
			'default'           => 'invoice-[number]',
			'custom_attributes' => array(
				'required' => true,
			),
			'desc'              => '<code>[number]</code> is the placeholder to display invoice number',
		);

		$invoice_settings[] = array(
			'name'    => __( 'Order Column', 'sdevs_pips' ),
			'id'      => 'pips_order_custom_column',
			'type'    => 'checkbox',
			'default' => 'yes',
			'desc'    => __( 'Order Column for direct view and download pdf (Admin)', 'sdevs_pips' ),
		);

		$invoice_settings[] = array(
			'name' => __( 'Disable for free orders', 'sdevs_pips' ),
			'id'   => 'pips_free_order_invoice',
			'type' => 'checkbox',
			// translators: currency with 0.
			'desc' => sprintf( __( 'Disable invoice when the order total is %s', 'sdevs_pips' ), wc_price( 0 ) ),
		);

		// view pdf.
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

		$invoice_settings[] = array(
			'name' => __( 'Invoice Logo', 'sdevs_pips' ),
			'id'   => 'pips_invoice_logo',
			'type' => 'text',
			'desc' => __( 'Select Logo', 'sdevs_pips' ),
		);

		// phpcs:ignore
		// $invoice_settings[] = array(
		// 'name'        => __( 'Logo height', 'sdevs_pips' ),
		// 'id'          => 'pips_invoice_logo_height',
		// 'type'        => 'text',
		// 'placeholder' => '3cm',
		// 'css'         => 'width: 6em;',
		// );

		$invoice_settings[] = array(
			'name'        => __( 'Shop Name', 'sdevs_pips' ),
			'id'          => 'pips_invoice_shop_name',
			'type'        => 'text',
			'placeholder' => get_bloginfo( 'name' ),
		);

		$invoice_settings[] = array(
			'name' => __( 'Default Invoice Note', 'sdevs_pips' ),
			'id'   => 'pips_invoice_note',
			'type' => 'textarea',
		);

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
