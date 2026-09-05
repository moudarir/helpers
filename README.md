# Moudarir Helpers

A collection of lightweight PHP helper classes with minimal external dependencies.

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Available Helpers](#available-helpers)
  - [Array Helper](#arrayhelper)
  - [String Helper](#stringhelper)
  - [Is Helper](#ishelper)
  - [Encryption Helper](#encryptionhelper)
  - [File Helper](#filehelper)
  - [Directory Helper](#directoryhelper)
- [License](#license)

## Requirements

- PHP 8.4 or higher

Some helpers require additional PHP extensions:

- `ext-ctype` for `IsHelper`;
- `ext-mbstring` for `StringHelper`;
- `ext-sodium` for `EncryptionHelper`.

## Installation

Install the package with Composer:

```bash
composer require moudarir/helpers
```

## Available Helpers

### ArrayHelper

`ArrayHelper` provides lightweight utility methods for common array operations, including extracting values, converting values to integers or strings, extracting image sources from HTML content, and comparing arrays.

#### `diff()`

Compares two arrays and determines which values have been added and which values have been removed.

The method returns whether a difference exists, the added values, and the removed values. It can optionally convert values from the new array to integers before performing the comparison.

#### `ids()`

Extracts values associated with a specified `key` from an array of `arrays` or `objects`.

Values that are missing or `null` are ignored. The extracted values are returned as a reindexed list.

#### `toInt()`

Converts valid integer values and integer strings into integers.

The method can restrict the result to positive integers and can optionally remove duplicate values. Invalid integer representations are ignored.

#### `toString()`

Converts integer and string values into strings.

The method can optionally reject empty values and remove duplicate values.

#### `extractImgSrc()`

Extracts the `src` attributes from `<img>` elements contained in an HTML string.

The method supports both single and double quotes and performs a case-insensitive search.

See the [ArrayHelper documentation](docs/ArrayHelper.md).

### StringHelper

`StringHelper` provides lightweight utility methods for common string operations, including extracting first letters, converting strings to camelCase, formatting byte sizes, and generating text excerpts.

#### `firstLetter()`

Returns the first character of a string with Unicode-aware case conversion.

#### `firstLetters()`

Extracts the first character of each word and joins them using a configurable separator.

#### `toCamelcase()`

Converts a separator-delimited string to camelCase.

#### `bytesToHuman()`

Converts a byte value into a human-readable representation using either binary (1024) or decimal (1000) units.

#### `excerpt()`

Creates a shortened text excerpt while removing HTML tags and supporting configurable ellipsis placement.

See the [StringHelper documentation](docs/StringHelper.md).

### IsHelper

`IsHelper` provides lightweight validation helpers for URLs, IP addresses, email addresses, MAC addresses, Base64 strings, numbers, hexadecimal values, and Arabic text.

#### `validUrl()`

Validates a URL and supports restricting the allowed protocols through `EnumProtocol`.

#### `validIP()`

Validates IPv4 and IPv6 addresses, optionally restricted to a specific IP version.

#### `validEmail()`

Validates an email address, including internationalized domains when supported by the environment.

#### `validMac()`

Validates a MAC address.

#### `validBase64()`

Validates a Base64-encoded string.

#### `natural()` and `naturalNoZero()`

Validate natural number strings, with `naturalNoZero()` excluding zero.

#### `alpha()` and `alphaNumeric()`

Validate alphabetic and alphanumeric strings.

#### `alphaNumericSpaces()`

Validates strings containing only letters, numbers, and spaces.

#### `alphaDash()`, `alnumDash()`, and `alnumUnderscore()`

Validate strings using common combinations of letters, numbers, hyphens, and underscores.

#### `numeric()`, `integer()`, and `decimal()`

Validate numeric string representations.

#### `hex()`

Validates hexadecimal values with an optional `0x` or `0X` prefix.

#### `containsArabic()` and `onlyArabic()`

`containsArabic()` checks whether a string contains Arabic characters, while `onlyArabic()` ensures that the string contains no letters from another script.

See the [IsHelper documentation](docs/IsHelper.md).

### EncryptionHelper

`EncryptionHelper` provides helpers for cryptographically secure random data, token generation, and authenticated symmetric encryption.

#### `binaryBytes()`

Generates cryptographically secure random bytes.

#### `generateToken()`

Generates cryptographically secure random tokens using configurable character sets and multiple parts.

#### `encrypt()`

Encrypts data using Sodium's authenticated `secretbox` encryption.

#### `decrypt()`

Decrypts data previously encrypted with `encrypt()`.

See the [EncryptionHelper documentation](docs/EncryptionHelper.md).

### FileHelper

`FileHelper` provides lightweight helpers for common file and path operations.

#### `newFilename()`

Generates an available filename while preserving the original file extension, with support for random or incremental filenames.

#### `pathInfo()`

Returns normalized information extracted from a file path.

#### `info()`

Returns metadata and optional filesystem information for an existing file.

#### `write()`

Writes data to a file with exclusive locking and support for partial writes.

#### `getContent()`

Reads the contents of a local file or URL.

#### `saveContent()`

Saves string data to a file and reports failures through `ErrorException`.

See the [FileHelper documentation](docs/FileHelper.md).

### DirectoryHelper

`DirectoryHelper` provides lightweight helpers for common directory operations.

#### `recursively()`

Returns all descendant directories recursively.

#### `map()`

Builds an array representation of a directory with configurable recursion, hidden-entry handling, and path output.

#### `create()`

Creates a directory and all missing parent directories.

#### `getFilesInfo()`

Returns information about files in a directory, with optional recursion.

#### `deleteFiles()`

Deletes directory contents recursively, with optional directory removal and protection of common web server files.

See the [DirectoryHelper documentation](docs/DirectoryHelper.md).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
