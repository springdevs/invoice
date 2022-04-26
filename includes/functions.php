<?php

function pips_pro_activated(): bool {
    return class_exists('Sdevs_pips_pro_main');
}
