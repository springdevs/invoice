<?php

namespace SpringDevs\Pips;

use SpringDevs\Pips\Admin\MetaBoxes;
use SpringDevs\Pips\Admin\Settings;
use SpringDevs\Pips\Illuminate\Invoice;

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
        new Settings;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
    }
}
