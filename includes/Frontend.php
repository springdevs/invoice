<?php

namespace SpringDevs\Pips;

use SpringDevs\Pips\Frontend\Order;
use SpringDevs\Pips\Illuminate\Email;

/**
 * Frontend handler class
 */
class Frontend
{
    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        new Order;
        new Email;
    }
}
