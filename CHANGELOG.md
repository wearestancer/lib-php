# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Refunds
- Disputes
- Add `ild78\Api\Config::setKeys()` method
- Custom user agent
- First issue/MR templates
- Setter aliases

### Changes
- Exception message will return API error message when possible
- `Config` handle multiple keys
- Test mode is set a default now
- Export only modified properties

### Fixed
- Formatted IBAN may have one space at end


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
