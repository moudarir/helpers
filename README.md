# Moudarir Helpers

A collection of lightweight, dependency-free PHP helper classes.

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Available Helpers](#available-helpers)
  - [Array Helper](#arrayhelper)
- [License](#license)

## Requirements

- PHP 8.4 or higher

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

See the [ArrayHelper documentation](/docs/ArrayHelper.md).

## License

This package is open-sourced software licensed under the MIT license.
