# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added
- Support PHP 8.0 & 8.1

### Changed
- No default timeout
- BIC is nore mandatory
- Depedencies versions


## [0.0.7] - 2021-05-06

### Fixed
- `Device` used server IP and port instead of customer IP and port
- PHPStan warnings
- Fix CI


## [0.0.6] - 2021-02-18

### Added
- `Stancer\Sepa` validation
- `Stancer\Core\Object::get()`, return raw API data (even unknown properties)
- `Stancer\Payment::isError()` and `Stancer\Payment::isNotError()`
- New `created_until` parameter on `Stancer\Payment::list()` and `Stancer\Dispute::list()` methods
- `Stancer\Payment::$currency` and `Stancer\Payment::$methodsAllowed` can throw exception when used with incompatible values
- `Stancer\Payment::$methodsAllowed`
- `Stancer\Payment::$responseAuthor`
- `Stancer\Payment\Status::CAPTURE_SENT`
- `Stancer\Http\Stream` and `Stancer\Http\Uri` to complete internal PSR7 implementation
- New supported currencies
- PHPStan validation
- Coverage report
- PHPCS report

### Changed
- `Stancer\Payment::list()` and `Stancer\Dispute::list()` uses `DateTimeInterface` in parameters
- `created` parameter accepts `DatePeriod` on `Stancer\Payment::list()` and `Stancer\Dispute::list()` methods
- `Stancer\Payment::isStatus()` and `Stancer\Payment::isNotStatus()` are now based on status

### Fixed
- `Stancer\Payment::$response` length
- Misstype in `InternalServerErrorException` default message
- PHPDoc
- Tests / CI run

### Removed
- `Stancer\Payment::$responseMessage`, please refer to documentation to obtain the full list
- Guzzle version from header, they remove the constant `VERSION` from the package


## [0.0.5] - 2020-10-02

### Added
- `Stancer\Payment::$dateBank`
- `Stancer\Refund::$dateBank`
- `Stancer\Refund::$dateRefund`
- `Stancer\Sepa::$mandate`
- `Stancer\Sepa::$dateMandate`
- `Stancer\Sepa` can be sent, retrieved or deleted to the API
- Date properties
- Allow to reset default timezone

### Changed
- `created` uses date property mecanism
- Magic setter can throw better exceptions

### Fixed
- Magic method in PHPDoc
- Misstype in tests


## [0.0.4] - 2020-09-15

### Fixed
- An error could be thrown on `Payment`'s update


## [0.0.3] - 2020-07-01

### Added
- 3DS support
- Refunds
- Disputes
- New `Stancer\Customer::$externalId` attribute
- New `Stancer\Card::$funding`, `Stancer\Card::$nature` and `Stancer\Card::$network` attributes
- `Stancer\Card` can be sent, retrieved or deleted to the API
- Add `Device` on every payment
- Allow payment without card or sepa
- Add `Stancer\Payment::getReturnUrl()` and `Stancer\Payment::setReturnUrl()` methods
- Add `Stancer\Payment::getPaymentPageUrl()` method
- Add `Stancer\Api\Config::setKeys()` method
- Hydration on instanciation
- Add `Stancer\Payment\Status`
- Custom user agent
- First issue/MR templates
- Setter aliases
- Tests on PHP 7.4

### Changed
- `Stancer\Payment::$status` is now editable
- No automatic hydratation on `Stancer\Device->new()`
- Getters and setters handles snake_case and camelCase property correctly
- Exception message will return API error message when possible
- `Config` handle multiple keys
- Test mode is set a default now
- Export only modified properties

### Fixed
- Formatted IBAN may have one space at end
- Amount casted to integer may lose 1 cts
- Warning during obfuscation

### Removed
- Support on PHP 7.1


## [0.0.2] - 2019-04-29

### Added
- `Payment::list()` method
- Coverage regression test (inner way to do it, Coveralls will be a better way but for later)


## [0.0.1] - 2019-04-10

### Added
- First implementation
- Tests on 7.1, 7.2 and 7.3

### Changed
- `Object` renamed in `AbstractObject`

### Fixed
- Prevent `Card` making `populate` call after successful payment
