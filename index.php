<?php
function dd() {
    $args = func_get_args();
    var_dump(...$args);
    die;
}

require_once 'vendor/autoload.php';

$exchange = new PhpCX\Exchange('binance');

$out = $exchange->trades('ETHBTC', 5);

//'ETHBTC', null, 1549234207925, 1549234207925, 2

print_r($out);
