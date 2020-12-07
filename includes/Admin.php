<?php

namespace SpringDevs\Pips;

use SpringDevs\Pips\Admin\MetaBoxes;
use SpringDevs\Pips\Admin\Order;
use SpringDevs\Pips\Admin\Settings;
use SpringDevs\Pips\Illuminate\Email;
use SpringDevs\Pips\Illuminate\Invoice;
use SpringDevs\WcSubscription\Frontend\Product;

/**
 * The admin class
 */
class Admin
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        $this->dispatch_actions();
        new MetaBoxes;
        new Invoice;
        new Order;
        new Settings;
        new Email;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        if (class_exists(Product::class)) new Product;
    }
}
