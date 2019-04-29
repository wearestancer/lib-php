# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `Payment::list()` method


## [0.0.1] - 2019-04-10

### Added
- First implementation
- Tests on 7.1, 7.2 and 7.3

### Changes
- `Object` renamed in `AbstractObject`

### Fixed
- Prevent `Card` making `populate` call after successful payment
