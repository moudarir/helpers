# Changelog

All notable changes to this project will be documented in this file.

## [1.3.0] - 2026-09-05

### Added

* Add `JsonHelper` for:
  * JSON encoding and decoding;
  * JSON-LD formatted output;
  * JSON Lines encoding and decoding.
* Add `SanitizeHelper` for:
  * URL-friendly slug generation;
  * URL title formatting;
  * accent removal and character transliteration.
* Add `NumberHelper` for:
  * number formatting;
  * percentage calculations;
  * secure integer code generation;
  * ceiling calculations.
* Add documentation for `JsonHelper`, `SanitizeHelper`, and `NumberHelper`.
* Add package badges to `README.md`.

### Changed

* Set `ext-intl` as a suggested extension for `SanitizeHelper`.
* Keep helper-specific PHP extensions as Composer suggestions rather than package-wide requirements.
* Update `README.md` with the newly added helpers.

## [1.2.0] - 2026-09-05

### Added

* Add `EncryptionHelper` for:
  * cryptographically secure random byte generation;
  * secure token generation;
  * authenticated symmetric encryption and decryption using Sodium.
* Add `FileHelper` for:
  * available filename generation;
  * normalized path information;
  * file metadata retrieval;
  * locked file writing;
  * file content reading and saving.
* Add `DirectoryHelper` for:
  * recursive directory listing;
  * directory tree mapping;
  * recursive directory creation;
  * file information retrieval;
  * recursive file deletion.
* Add documentation for `EncryptionHelper`, `FileHelper` and `DirectoryHelper`;
* Add `ext-sodium` to the package requirements.

### Changed

* Update `README.md` with the newly added helpers.
* Set required extensions as suggested in `composer.json`.


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
