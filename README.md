Quandl Elephant API
===================

This project is a fork of [php-quandl](https://github.com/DannyBen/php-quandl), a great API to provide easy access to the 
[Quandl API](https://www.quandl.com/help/api) using PHP.

The name of this project was changed to answer the [PHP Licensing](http://php.net/license/).

[![Build Status](https://travis-ci.org/royopa/quandl-elephant-api.svg?branch=master)](https://travis-ci.org/royopa/quandl-elephant-api)
[![Latest Stable Version](https://poser.pugx.org/royopa/quandl-elephant-api/v/stable)](https://packagist.org/packages/royopa/quandl-elephant-api) [![Total Downloads](https://poser.pugx.org/royopa/quandl-elephant-api/downloads)](https://packagist.org/packages/royopa/quandl-elephant-api) [![Latest Unstable Version](https://poser.pugx.org/royopa/quandl-elephant-api/v/unstable)](https://packagist.org/packages/royopa/quandl-elephant-api) [![License](https://poser.pugx.org/royopa/quandl-elephant-api/license)](https://packagist.org/packages/royopa/quandl-elephant-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/royopa/quandl-elephant-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/royopa/quandl-elephant-api/?branch=master)

Install
-------

To install with composer:

```sh
composer require royopa/quandl-elephant-api
```

Examples
--------

This is a basic call. It will return a PHP object with price
data for AAPL:

```php
$api_key = "YOUR_KEY_HERE";
$quandl  = new Quandl($api_key);
$data    = $quandl->getSymbol("GOOG/NASDAQ_AAPL");
```

You may pass any parameter that is mentioned in the Quandl
documentation:

```php
$quandl = new Quandl($api_key);
$data = $quandl->getSymbol($symbol, [
	"sort_order"      => "desc",
	"exclude_headers" => true,
	"rows"            => 10,
	"column"          => 4, 
]);
```

The date range options get a special treatment. You may use
any date string that PHP's `strtotime()` understands.

```php
$quandl = new Quandl($api_key, "csv");
$data = $quandl->getSymbol($symbol, [
	"trim_start" => "today-30 days",
	"trim_end"   => "today",
]);
```

You can also search the entire Quandl database and get a list of
supported symbols in a data source:

```php
$quandl = new Quandl($api_key);
$data   = $quandl->getSearch("crude oil");
$data   = $quandl->getList("WIKI", 1, 10);
```

More examples can be found in the [examples.php](https://github.com/DannyBen/php-quandl/blob/master/examples.php) file 

Caching
-------

You may provide the `quandl` object with a cache handler function.
This function should be responsible for both reading from your cache and storing to it. 

See the [example_cache.php](https://github.com/DannyBen/php-quandl/blob/master/example_cache.php) file.


Reference
---------

### Constructor and public properties

The constructor accepts two optional parameters: `$api_key` and `$format`:

```php
$quandl = new Quandl("YOUR KEY", "csv");
```

You may also set these properties later:

```php
$quandl->api_key = "YOUR KEY";
$quandl->format  = "json";
```

`$format` can be one of `csv`, `xml`, `json`, and `object` (which will return a php object obtained with `json_decode()`).

After each call to Quandl, the property `$last_url` will be set 
for debugging and other purposes. In case there was an error getting
the data from Quandl, the result will be `false` and the property 
`$error` will contain the error message.


### getSymbol

```php
mixed getSymbol( string $symbol [, array $params ] )
```

Returns an object containing data for a given symbol. The format
of the result depends on the value of `$quandl->format`.

The optional parameters array is an associative `key => value`
array with any of the parameters supported by Quandl.

You do not need to pass `auth_token` in the array, it will be 
automatically appended.


### getSearch

```php
mixed getSearch( string $query [, int $page, int $per_page] )
```

Returns a search result object. Number of results per page is 
limited to 300 by default.

Note that currently Quandl does not support CSV response for this 
node so if `$quandl->format` is "csv", this call will return a JSON
string instead.


### getList

```php
mixed getList( string $source [, int $page, int $per_page] )
```

Returns a list of symbols in a given source. Number of results per page is limited to 300 by default.


Tests
-----

From the project directory, tests can be ran using:

```sh    
./vendor/bin/phpunit
```
