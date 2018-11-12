# Ild78 PHP library

## Requirement

We only support current PHP version, so this library needs at least PHP 7.1.


## Installation

To keep it simple, we uses [Composer](http://getcomposer.org/).
You only need to run the following commmand :

```bash
composer require ild78/lib-php
```

The library uses [Guzzle](https://packagist.org/packages/guzzlehttp/guzzle) internaly to connect to the API.
It will automaticaly be installed during `composer` process.


## Usage

All you need is a valid API key.

```php
<?php

$key = 'my_api_key_kSp7hBH3hyDQ36izsyKR';

ild78\Api\Config::init($key);

$card = new ild78\Card;
$card->setNumber('4111111111111111');
$card->setExpMonth(12);
$card->setExpYear(2022);
$card->setCvc('999');
$card->setName('John Doe');

$payment = new ild78\Payment;
$payment->setCard($card);
$payment->setAmmount(1000); // You must put an integer, here we wanted USD$10.00
$payment->setCurrency('USD');
$payment->description('My first payment');
$payment->save();
```


### Retrieve an object

To retrieve an object, just put an identifier when creating a new instance.

```php
<?php

$payment = new ild78\Payment('paym_KIVaaHi7G8QAYMQpQOYBrUQE');
```


### Creating an object

To create an object, like a payment, use the `save` method.

```php
<?php

$payment = new ild78\Payment;

$payment->setCard($card); // Pay with a card
// or
$payment->setSepa($sepa); // Pay with a bank account (SEPA, uses BIC and IBAN)

// Do not forget to complete your payment informations

$payment->save();
```


### Exceptions

Every exceptions thrown by this project extend from `ild78\Exceptions\Exception`.

Some exceptions are related to the connection with the API :
* `ild78\Exceptions\TooManyRedirectsException` on 310 HTTP errors
* `ild78\Exceptions\BadRequestException` on 400 HTTP errors
* `ild78\Exceptions\NotAuthorizedException` on 401 HTTP errors (basically bad credential)
* `ild78\Exceptions\NotFoundException` on 404 HTTP errors
* `ild78\Exceptions\ConflictException` on 409 HTTP errors

Or with less specificity :
* `ild78\Exceptions\RedirectionException` on 3** HTTP errors (technically only Too many redirection)
* `ild78\Exceptions\ClientException` on 4** HTTP errors
* `ild78\Exceptions\ServerException` on 5** HTTP errors


You can see other exceptions, not related to HTTP traffic :
* `ild78\Exceptions\BadMethodCallException` when you are calling an unknown method
* `ild78\Exceptions\InvalidArgumentException` when you are using a method without the right arguments

For these two exceptions, you need to check you code, they should never appear in real world.

To see every available exceptions, simply take a look in `src/Exceptions` folder.

### Logger

You can add a [PSR3 compatible logger](https://www.php-fig.org/psr/psr-3/).

We suggeset [monolog](https://seldaek.github.io/monolog/) but anything implementing
[log interfaces](https://github.com/php-fig/log) can be use.

```php
<?php
$log = new Monolog\Logger('ild78');
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
