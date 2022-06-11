<?php

namespace SpringDevs\WcPips;

use Dompdf\Dompdf;

/**
 * The Ajax class
 */
class Ajax
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        add_action('wp_ajax_pips_save_order_pdf', [$this, 'pips_save_order_pdf']);
    }

    public function pips_save_order_pdf()
    {
        $dompdf = new Dompdf;
        $html = $this->render_template(PIPS_PATH . '/templates/simple/template.php');
        $options = $dompdf->getOptions();
        $options->set('chroot', PIPS_PATH);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('hello world', ['Attachment' => 0]);
        wp_send_json($_POST);
    }

    public function render_template($file)
    {
        ob_start();
        if (file_exists($file)) include($file);
        return ob_get_clean();
    }
}
