# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2024-11-15

### Added
- Force parameter for payment page (CMS-259)


## [2.0.0] - 2024-03-06

### Breaking
- Drop support for PHP 7.4 and 8.0
- Every status are now enum

### Added
- `Stancer\Currency`
- `Stancer\Payment\MethodsAllowed`
- More typing
- PHP 8.3 support

### Changed
- `Stancer\Auth\Status` is an enum now
- `Stancer\Dispute\Status` is an enum now
- `Stancer\Refund\Status` is an enum now
- `Stancer\Payment\Status` is an enum now
- `Stancer\Payout\Status` is an enum now
- Bump version of `psr/http-message` and `psr/log`, CMS-82, [GitLab#5](https://gitlab.com/wearestancer/library/lib-php/-/issues/5), [GitHub#12](https://github.com/wearestancer/lib-php/issues/12)

### Fixed
- Some tests could fail due to unanticipated random data


## [1.1.3] - 2024-02-20

### Added
- `$created`/`getCreated()` PHPDoc

### Changed
- `bool` changed to `boolean`

### Fixed
- `Stancer\Payment::$auth`/`Stancer\Payment::set_auth()` had bad typing

### Removed
- `Stancer\Core\AbstractObject::dataModel{Adder,Getter,Setter}()` are no longer public


## [1.1.2] - 2024-02-16

### Added
- Classes and methods are marked for future changes
- Documentation attributes
- Some parameters are marked as sensitive
- `Stancer\Payment::charge()` is deprecated
- `Stancer\Payment::pay()` is deprecated

### Changed
- Change minimum length of `name` attribute for Card, Customer, Sepa from 4 to 3 (same behaviour as the v1 API).
- `devcontainer` configuration for VS Code

### Fixed
- Better PHPDoc
- Some methods/properties were not correctly defined


## [1.1.1] - 2023-09-22

### Changed
- A payment without capture can be done with `amount = 0`
- `AbstractObject->send()` will now throw `BadMethodCallException` if use on an empty object

### Fixed
- `RequestTimeoutException` is thrown on curl timeout
- Typo


## [1.1.0] - 2023-02-15

### Added
- Allow to add app data in user agent
- `Payout` [GitHub#5](https://github.com/wearestancer/lib-php/issues/5)
- `Refund::list()`

### Fixed
- `SearchTrait::List()` may miss some items [GitLab#2](https://gitlab.com/wearestancer/library/lib-php/-/issues/2)
- CI


## [1.0.1] - 2023-01-23

### Fixed
- Missing payment status `refused`
- Typo


## [1.0.0] - 2022-09-26
- Initial release
