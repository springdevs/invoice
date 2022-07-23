<?php

namespace SpringDevs\WcPips;

use SpringDevs\WcPips\Frontend\Order;
use SpringDevs\WcPips\Illuminate\Email;

/**
 * Frontend handler class
 */
class Frontend {

	/**
	 * Frontend constructor.
	 */
	public function __construct() {
		new Order();
		new Email();
	}
}
