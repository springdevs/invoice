<?php

namespace SpringDevs\WcPips;

use SpringDevs\WcPips\Admin\Links;
use SpringDevs\WcPips\Admin\MetaBoxes;
use SpringDevs\WcPips\Admin\Order;
use SpringDevs\WcPips\Admin\Settings;
use SpringDevs\WcPips\Illuminate\Email;
use SpringDevs\WcPips\Illuminate\Invoice;

/**
 * The admin class
 */
class Admin {


	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->dispatch_actions();
		new Links();
		new MetaBoxes();
		new Invoice();
		new Order();
		new Settings();
		new Email();
	}

	/**
	 * Dispatch and bind actions
	 *
	 * @return void
	 */
	public function dispatch_actions() {
		add_filter(
			'woocommerce_get_settings_pages',
			function ( $settings ) {
				$settings[] = require_once __DIR__ . '/Utils/Settings.php';
				return $settings;
			}
		);
	}
}
