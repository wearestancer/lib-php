# Stancer PHP library

## Requirement

We only support current PHP version, so this library needs at least PHP 7.4.


## Installation

To keep it simple, we uses [Composer](http://getcomposer.org/).
You only need to run the following commmand :

```bash
composer require stancer/stancer
```

The library uses [cURL](https://secure.php.net/manual/fr/book.curl.php) internaly to connect to the API.
You need the php extension `phpX.Y-curl`, where `X.Y` correspond to your PHP version, to be installed on your server.

You may also use [Guzzle](https://packagist.org/packages/guzzlehttp/guzzle)
or every [PSR7 compatible client](https://www.php-fig.org/psr/psr-7/) to replace the internal cURL instance.
You only need to provider the client instance to the API config.

```php
<?php

$config = Stancer\Config::init($key);
$config->setHttpClient($guzzle);
```


## Usage

All you need is a valid API key.

```php
<?php

$key = 'my_api_key_kSp7hBH3hyDQ36izsyKR';

Stancer\Config::init($key);

$card = new Stancer\Card();
$card->setNumber('4111111111111111');
$card->setExpMonth(12);
$card->setExpYear(2022);
$card->setCvc('999');
$card->setName('John Doe');

$payment = new Stancer\Payment();
$payment->setCard($card);
$payment->setAmount(1000); // You must put an integer, here we wanted USD$10.00
$payment->setCurrency('USD');
$payment->description('My first payment');
$payment->send();
```


### Retrieve an object

To retrieve an object, just put an identifier when creating a new instance.

```php
<?php

$payment = new Stancer\Payment('paym_KIVaaHi7G8QAYMQpQOYBrUQE');
```


### Creating an object

To create an object, like a payment, use the `send` method.

```php
<?php

$payment = new Stancer\Payment();

$payment->setCard($card); // Pay with a card
// or
$payment->setSepa($sepa); // Pay with a bank account (SEPA, uses BIC and IBAN)

// Do not forget to complete your payment informations

$payment->send();
```


### Refund a payment

The easiest way to do it, use `Payment::refund()` method.
There is a simple example :

```php
<?php

$payment = new Stancer\Payment('paym_KIVaaHi7G8QAYMQpQOYBrUQE');

$payment->refund(); // To refund the full payment
// or
$payment->refund($amount); // To refund a particular amount


$payment->getRefunds(); // Will return all refunds made on a payment
```


### Exceptions

Every exceptions thrown by this project extend from `Stancer\Exceptions\Exception`.

Some exceptions are related to the connection with the API :
* `Stancer\Exceptions\TooManyRedirectsException` on 310 HTTP errors
* `Stancer\Exceptions\BadRequestException` on 400 HTTP errors
* `Stancer\Exceptions\NotAuthorizedException` on 401 HTTP errors (basically bad credential)
* `Stancer\Exceptions\NotFoundException` on 404 HTTP errors
* `Stancer\Exceptions\ConflictException` on 409 HTTP errors

Or with less specificity :
* `Stancer\Exceptions\RedirectionException` on 3** HTTP errors (technically only Too many redirection)
* `Stancer\Exceptions\ClientException` on 4** HTTP errors
* `Stancer\Exceptions\ServerException` on 5** HTTP errors


You can see other exceptions, not related to HTTP traffic :
* `Stancer\Exceptions\BadMethodCallException` when you are calling an unknown method
* `Stancer\Exceptions\InvalidArgumentException` when you are using a method without the right arguments

For these two exceptions, you need to check you code, they should never appear in real world.

To see every available exceptions, simply take a look in `src/Exceptions` folder.

### Logger

You can add a [PSR3 compatible logger](https://www.php-fig.org/psr/psr-3/).

We suggeset [monolog](https://seldaek.github.io/monolog/) but anything implementing
[log interfaces](https://github.com/php-fig/log) can be use.

```php
<?php
$log = new Monolog\Logger('Stancer');
$log->pushHandler(new Monolog\Handler\StreamHandler('path/to/your.log', Monolog\Logger::INFO));

$config->setLogger($logger);
```


## Security

* Never, never, NEVER register a card or a bank account number in your database.

* Always uses HTTPS in card/SEPA in communication.

* Our API will never give you a complete card/SEPA number, only the last four digits.
If you need to keep track, use these last four digit.


## Contribute

We can contribute and modify everything, you just need to follow some rules :
* Fork the project
* Make a topic branch (aka one branch for one feature or one bug)
* Test locally
* Complete the `Unreleased` part of the changelog
* Make a merge/pull request

Every MR/PR MUST have unit test and `CHANGELOG` entries to be approved.

Our unit testing framework is [atoum](http://atoum.org/).
It is fast and easy to learn.
Go see the [documentation](http://docs.atoum.org/en/latest/).
