# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added
- `ild78\Sepa::$mandate`
- `ild78\Sepa::$dateMandate`
- `ild78\Sepa` can be sent, retrieved or deleted to the API
- Date properties
- Allow to reset default timezone

### Changes
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
- New `ild78\Customer::$externalId` attribute
- New `ild78\Card::$funding`, `ild78\Card::$nature` and `ild78\Card::$network` attributes
- `ild78\Card` can be sent, retrieved or deleted to the API
- Add `Device` on every payment
- Allow payment without card or sepa
- Add `ild78\Payment::getReturnUrl()` and `ild78\Payment::setReturnUrl()` methods
- Add `ild78\Payment::getPaymentPageUrl()` method
- Add `ild78\Api\Config::setKeys()` method
- Hydration on instanciation
- Add `ild78\Payment\Status`
- Custom user agent
- First issue/MR templates
- Setter aliases
- Tests on PHP 7.4

### Changes
- `ild78\Payment::$status` is now editable
- No automatic hydratation on `ild78\Device->new()`
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

### Changes
- `Object` renamed in `AbstractObject`

### Fixed
- Prevent `Card` making `populate` call after successful payment
