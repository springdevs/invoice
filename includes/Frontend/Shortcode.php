<?php

namespace SpringDevs\Pips\Frontend;

/**
 * Class Shortcode
 * @package SpringDevs\Pips\Frontend
 */
class Shortcode {

    public function __construct() {
        add_shortcode( 'pdf_invoices_&_packing_slips', [ $this, 'render_frontend' ] );
    }

    /**
     * Render frontend app
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function render_frontend( $atts, $content = '' ) {
        // wp_enqueue_style( 'frontend' );
        // wp_enqueue_script( 'frontend' );

        $content .= 'Hello World!';

        return $content;
    }
}
