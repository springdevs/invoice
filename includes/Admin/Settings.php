<?php

namespace SpringDevs\Pips\Admin;

/**
 * Settings class
 * Woocommerce Settings Tabs
 */
class Settings
{
    public function __construct()
    {
        add_filter('woocommerce_get_sections_wcma', [$this, 'add_section'], 30);
        add_filter('woocommerce_get_settings_wcma', [$this, 'settings_content']);
    }

    public function add_section($sections)
    {
        $sections['invoice'] = __('Invoices', 'sdevs_wea');
        $sections['packing-slip'] = __('Packing Slips', 'sdevs_wea');
        return $sections;
    }

    public function settings_content($settings)
    {
        global $current_section;
        if ('invoice' === $current_section) :
            return $this->invoice_settings();
        elseif ('packing-slip' === $current_section) :
            return $this->packing_slip_settings();
        endif;
        return $settings;
    }

    public function invoice_settings()
    {
        $invoice_settings = [];

        $invoice_settings[] = [
            'name' => __('Invoice Settings', 'sdevs_wea'),
            'type' => 'title',
            'desc' => __('The following options are used to configure Invoice Module', 'sdevs_wea'),
            'id' => 'invoice'
        ];

        // Enable / Disable Invoice
        $invoice_settings[] = array(
            'name'     => __('Enable Invoice', 'sdevs_wea'),
            'id'       => 'pips_enable_invoice',
            'type'     => 'checkbox',
            'default'  => 'yes',
            'desc' => __('Enable or Disable invoice feature', 'sdevs_wea')
        );

        // attach pdf [ new order ]
        $invoice_settings[] = array(
            'name'     => __('Attach to', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_new_order_admin',
            'type'     => 'checkbox',
            'checkboxgroup'   => 'start',
            'default'  => 'yes',
            'desc'     => __('New order (Admin email)', 'sdevs_wea')
        );

        // attach pdf [ Cancelled order ]
        $invoice_settings[] = array(
            'name'     => __('Cancelled order', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_cancelled_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Cancelled order', 'sdevs_wea'),
        );

        // attach pdf [ Failed order ]
        $invoice_settings[] = array(
            'name'     => __('Failed order', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_failed_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Failed order', 'sdevs_wea'),
        );

        // attach pdf [ Order on-hold ]
        $invoice_settings[] = array(
            'name'     => __('Order on-hold', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_on-hold_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Order on-hold', 'sdevs_wea'),
        );

        // attach pdf [ Processing order ]
        $invoice_settings[] = array(
            'name'     => __('Processing order', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_processing_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Processing order', 'sdevs_wea'),
        );

        // attach pdf [ Completed order ]
        $invoice_settings[] = array(
            'name'     => __('Completed order', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_completed_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Completed order', 'sdevs_wea'),
        );

        // attach pdf [ Refunded order ]
        $invoice_settings[] = array(
            'name'     => __('Refunded order', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_refunded_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Refunded order', 'sdevs_wea'),
        );

        // attach pdf [ Customer note ]
        $invoice_settings[] = array(
            'name'     => __('Customer note', 'sdevs_wea'),
            'id'       => 'pips_attach_pdf_admin_note_order',
            'type'     => 'checkbox',
            'checkboxgroup'   => 'end',
            'default'  => 'yes',
            'desc'     => __('Customer note', 'sdevs_wea'),
        );

        // Disable for:
        $invoice_settings[] = array(
            'title'     => __('Disable for', 'sdevs_wea'),
            'id'       => 'pips_invoice_disable_statuses',
            'class'    => 'wc-enhanced-select',
            'type'     => 'multiselect',
            'options'  => function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : []
        );

        // Paper Size
        $invoice_settings[] = array(
            'title'     => __('Paper Size', 'sdevs_wea'),
            'id'       => 'pips_invoice_paper_size',
            'type'     => 'select',
            'options'  => [
                'a4'       => __("A4", "sdevs_wea"),
                'letter'   => __("Letter", "sdevs_wea"),
            ]
        );

        // Display shipping address
        $invoice_settings[] = array(
            'name'     => __('Display shipping address', 'sdevs_wea'),
            'id'       => 'pips_invoice_display_shipping_address',
            'type'     => 'select',
            'options'  => [
                'no' => __('No', 'sdevs_wea'),
                'when_different' => __('Only when different from billing address', 'sdevs_wea'),
                'always' => __('Always', 'sdevs_wea'),
            ],
            'default'  => 'when_different',
        );

        // display invoice number
        $invoice_settings[] = array(
            'name'     => __('Show / Hide', 'sdevs_wea'),
            'id'       => 'pips_display_invoice_number',
            'type'     => 'checkbox',
            'checkboxgroup'   => 'start',
            'desc'     => __('Invoice Number', 'sdevs_wea')
        );

        // Enable invoice date
        $invoice_settings[] = array(
            'name'     => __('Invoice Date', 'sdevs_wea'),
            'id'       => 'pips_display_invoice_date',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Invoice Date', 'sdevs_wea'),
        );

        // display email address
        $invoice_settings[] = array(
            'name'     => __('Email address', 'sdevs_wea'),
            'id'       => 'pips_display_user_email',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Email address', 'sdevs_wea')
        );

        // Display phone number
        $invoice_settings[] = array(
            'name'     => __('Phone number', 'sdevs_wea'),
            'id'       => 'pips_display_user_phone',
            'type'     => 'checkbox',
            'checkboxgroup'   => '',
            'desc'     => __('Phone number', 'sdevs_wea')
        );

        // Display customer note
        $invoice_settings[] = array(
            'name'     => __('Display customer note', 'sdevs_wea'),
            'id'       => 'pips_display_customer_note',
            'type'     => 'checkbox',
            'checkboxgroup'   => 'end',
            'default'  => 'yes',
            'desc'     => __('Display customer note', 'sdevs_wea'),
        );

        // custom colum
        $invoice_settings[] = array(
            'name'     => __('Custom Column', 'sdevs_wea'),
            'id'       => 'pips_order_custom_column',
            'type'     => 'checkbox',
            'default'  => 'yes',
            'desc' => __('Order Column for direct view or download pdf (Admin)', 'sdevs_wea')
        );

        // Disable for free orders
        $invoice_settings[] = array(
            'name'     => __('Disable for free orders', 'sdevs_wea'),
            'id'       => 'pips_free_order_invoice',
            'type'     => 'checkbox',
            'desc' => __('Disable invoice when the order total is ' . wc_price(0), 'sdevs_wea')
        );

        // view pdf
        $invoice_settings[] = array(
            'name'     => __('view PDF', 'sdevs_wea'),
            'id'       => 'pips_view_invoice_front',
            'type'     => 'select',
            'options'  => [
                'download' => __('Download the PDF', 'sdevs_wea'),
                'display' => __('Open the PDF in a new browser tab/window', 'sdevs_wea')
            ],
            'default' => 'display',
            'desc' => __('How do you want to view the PDF ?', 'sdevs_wea'),
            'desc_tip' => true
        );

        // Logo Upload
        $invoice_settings[] = array(
            'name'     => __('Invoice Logo', 'sdevs_wea'),
            'id'       => 'pips_invoice_logo',
            'type'     => 'text',
            'desc' => __('Logo URL for invoice', 'sdevs_wea')
        );

        // Logo height
        $invoice_settings[] = array(
            'name'     => __('Logo height', 'sdevs_wea'),
            'id'       => 'pips_invoice_logo_height',
            'type'     => 'text',
            'placeholder' => '3cm',
            'css'      => 'width: 6em;'
        );

        // Shop Name
        $invoice_settings[] = array(
            'name'     => __('Shop Name', 'sdevs_wea'),
            'id'       => 'pips_invoice_shop_name',
            'type'     => 'text',
            'placeholder' => 'Enter Shop Name'
        );

        // Shop Address
        $invoice_settings[] = array(
            'name'     => __('Shop Address', 'sdevs_wea'),
            'id'       => 'pips_invoice_shop_address',
            'type'     => 'textarea'
        );

        // Default Invoice Note
        $invoice_settings[] = array(
            'name'     => __('Default Invoice Note', 'sdevs_wea'),
            'id'       => 'pips_invoice_note',
            'type'     => 'textarea'
        );

        // Footer
        $invoice_settings[] = array(
            'name'     => __('Footer', 'sdevs_wea'),
            'id'       => 'pips_invoice_footer_note',
            'type'     => 'textarea'
        );

        $invoice_settings[] = array('type' => 'sectionend', 'id' => 'invoice');
        return $invoice_settings;
    }

    public function packing_slip_settings()
    {
        $fields = array(
            [
                'name' => __('Packing Slips Settings', 'sdevs_wea'),
                'type' => 'title',
                'desc' => __('The following options are used to configure Invoice Module', 'sdevs_wea'),
                'id'   => 'packing-slip'
            ],
            [
                'name'     => __('Enable Packing Slips', 'sdevs_wea'),
                'id'       => 'pips_enable_packing_slip',
                'type'     => 'checkbox',
                'default'  => 'yes',
                'desc' => __('Enable or Disable packing slips feature', 'sdevs_wea')
            ],
            [
                'name'     => __('Display email address', 'sdevs_wea'),
                'id'       => 'pips_packing_slip_display_email',
                'type'     => 'checkbox',
            ],
            [
                'name'     => __('Display phone number', 'sdevs_wea'),
                'id'       => 'pips_packing_slip_display_phone',
                'type'     => 'checkbox',
            ],
            [
                'name'     => __('Display note', 'sdevs_wea'),
                'id'       => 'pips_packing_slip_display_note',
                'type'     => 'checkbox',
            ],
            [
                'name'     => __('Default Note', 'sdevs_wea'),
                'id'       => 'pips_packing_slip_note',
                'type'     => 'textarea'
            ],
            [
                'name'     => __('Display customer notes', 'sdevs_wea'),
                'id'       => 'pips_packing_slip_display_customer_note',
                'type'     => 'checkbox',
            ],
            [
                'type' => 'sectionend',
                'id' => 'packing-slip'
            ]
        );
        return $fields;
    }
}
