<?php

namespace SpringDevs\Pips\Illuminate;

/**
 * Handle Emails
 *
 * Class Email
 * @package SpringDevs\Pips\Illuminate
 */
class Email
{
    public function __construct()
    {
        add_filter('woocommerce_email_attachments', [$this, 'include_pdf_with_email'], 10, 4);
        add_action('init', [$this, 'clean_directory']);
    }

    public function include_pdf_with_email($attachments, $email_id, $order, $email)
    {
        $email_ids = ['customer_invoice'];

        if ('yes' === get_option('pips_free_order_invoice', 'no')) return $attachments;

        if ('yes' === get_option('pips_attach_pdf_new_order_admin', 'yes')) $email_ids[] = 'new_order';
        if ('yes' === get_option('pips_attach_pdf_cancelled_order', 'no')) $email_ids[] = 'cancelled_order';
        if ('yes' === get_option('pips_attach_pdf_failed_order', 'no')) $email_ids[] = 'failed_order';
        if ('yes' === get_option('pips_attach_pdf_on-hold_order', 'no')) $email_ids[] = 'on-hold_order';
        if ('yes' === get_option('pips_attach_pdf_processing_order', 'no')) $email_ids[] = 'customer_processing_order';
        if ('yes' === get_option('pips_attach_pdf_completed_order', 'no')) $email_ids[] = 'completed_order';
        if ('yes' === get_option('pips_attach_pdf_refunded_order', 'no')) $email_ids[] = 'refunded_order';
        if ('yes' === get_option('pips_attach_pdf_admin_note_order', 'no')) $email_ids[] = 'customer_note';

        $disable_statuses = get_option('pips_invoice_disable_statuses', []);
        foreach ($disable_statuses as $disable_statuse) if ($order->has_status(substr($disable_statuse, 3))) return $attachments;

        if (in_array($email_id, $email_ids)) {
            $invoice = new Invoice;
            $file_path = $invoice->generate_save_pdf($order->get_id());
            if (file_exists($file_path)) {
                $attachments[] = $file_path;
                $path_options = get_option('pips_save_pdfs', []);
                array_push($path_options, $file_path);
                update_option('pips_save_pdfs', $path_options);
            }
        }
        return $attachments;
    }

    public function clean_directory()
    {
        $path_options = get_option('pips_save_pdfs', []);
        foreach ($path_options as $file_path) if (file_exists($file_path)) unlink($file_path);
        delete_option('pips_save_pdfs');
    }
}
