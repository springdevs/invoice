<?php

namespace SpringDevs\Pips;

/**
 * Class Installer
 * @package SpringDevs\Pips
 */
class Installer
{
    /**
     * Run the installer
     *
     * @return void
     */
    public function run()
    {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Add time and version on DB
     */
    public function add_version()
    {
        $installed = get_option('PDF Invoices & Packing Slips_installed');

        if (!$installed) {
            update_option('PDF Invoices & Packing Slips_installed', time());
        }

        update_option('PDF Invoices & Packing Slips_version', SDEVS_PIPS_VERSION);
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_tables()
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $this->create_pips_invoices_table();
    }

    /**
     * Create pips_invoices table
     *
     * @return void
     */
    public function create_pips_invoices_table()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = $wpdb->prefix . 'pips_invoices';

        $schema = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `order_id` VARCHAR(255) NOT NULL,
                      `invoice_type` VARCHAR(255) NOT NULL,
                      `data` TEXT,
                      PRIMARY KEY (`id`)
                    ) $charset_collate";

        dbDelta($schema);
    }
}
