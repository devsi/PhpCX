# PhpCX

PhpCX is a PHP library enabling quick API access to various Cryptocurrency Exchanges. The vision is to allow yaml definition of new exchanges for quick integration, and a single unified selection of method calls for easy connection to exchange APIs.

Only public endpoints will be available for version 1. Version 2 will aim to add private endpoints for market trades and user data.

## Usage

```php
$exchange = new PhpCX\Exchange('binance');

$trades = $exchange->trades('ETHBTC');
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)