<?php
function dd() {
    $args = func_get_args();
    var_dump(...$args);
    die;
}

require_once 'vendor/autoload.php';

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$exchange = new PhpCX\Exchange('binance');

$out = $exchange->klines('ETHBTC', '1d');

print_r($out);
