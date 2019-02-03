<?php
function dd() {
    $args = func_get_args();
    var_dump(...$args);
    die;
}

require_once 'vendor/autoload.php';

$binance = new PhpCX\Exchange('binance');

$out = $binance->book('ETHBTC', 5);

print_r($out);