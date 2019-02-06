<?php
function dd() {
    $args = func_get_args();
    var_dump(...$args);
    die;
}

require_once 'vendor/autoload.php';

$exchange = new PhpCX\Exchange('bitstamp');

$out = $exchange->trades('btcusd', 'day');

print_r($out);
