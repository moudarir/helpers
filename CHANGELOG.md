# Changelog

All notable changes to this project will be documented in this file.

## [1.1.0] - 2026-09-04

### Added

* Add `StringHelper` with common string utilities:
  * Unicode-aware first-letter extraction;
  * first-letter extraction for multiple words;
  * camelCase conversion;
  * human-readable byte formatting;
  * text excerpt generation.
* Add `IsHelper` with validation helpers for:
  * URLs and configurable protocols;
  * IPv4 and IPv6 addresses;
  * email addresses;
  * MAC addresses;
  * Base64 strings;
  * natural, integer, decimal, and numeric strings;
  * hexadecimal values;
  * alphabetic and alphanumeric strings;
  * Arabic text detection.
* Add `EnumProtocol` to define the protocols supported by `IsHelper::validUrl()`.
* Add package documentation for `StringHelper` and `IsHelper`.

### Changed

* Update package requirements to include `ext-ctype` and `ext-mbstring` extensions.
* Update `Github Actions CI` to include `ext-ctype` and `ext-mbstring` extensions.
* Update `ArrayHelper` documentation.


## [1.0.0] - 2026-09-03

### Added

- Added `ArrayHelper` with the following methods:
  - `diff()`
  - `ids()`
  - `toInt()`
  - `toString()`
  - `extractImgSrc()`
