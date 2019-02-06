# PhpCX

PhpCX is a PHP library enabling quick API access to various Cryptocurrency Exchanges. The vision is to allow yaml definition of new exchanges for quick integration, and a single unified selection of method calls for easy connection to exchange APIs.

Only public endpoints will be available for version 1. Version 2 will aim to add private endpoints for market trades and user data.

## Usage

```php
// create an exchange instance
$exchange = new PhpCX\Exchange('binance');

// access an endpoint defined in the config
$trades = $exchange->trades('ETHBTC');
```

**Caching** is enabled by default on all public endpoints. Use the  following options to configure the cache.

```php
// disable content caching
$exchange->disableCache();

// likewise enable content caching
$exchange->enableCache();

// set cache ttl to 10 seconds
$exchange->setCacheTtl(10);

// change the cache provider
$exchange->setCache(CacheInterface $cache);

```
The default cache provider is the Symfony `PhpFilesAdapter`.
A list of providers can be found [here](https://symfony.com/doc/current/components/cache/cache_pools.html).

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)