<?php
function dd() {
    $args = func_get_args();
    var_dump(...$args);
    die;
}

require_once 'vendor/autoload.php';

$exchange = new PhpCX\Exchange('binance');

$out = $exchange->ticker('ETHBTC');

print_r($out);
