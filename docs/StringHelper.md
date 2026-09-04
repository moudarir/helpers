# StringHelper

`StringHelper` provides lightweight utility methods for common string operations, including extracting first letters, converting strings to camelCase, formatting byte sizes, and generating text excerpts.

## Available Methods

* [`firstLetter()`](#firstletter)
* [`firstLetters()`](#firstletters)
* [`toCamelcase()`](#tocamelcase)
* [`bytesToHuman()`](#bytestohuman)
* [`excerpt()`](#excerpt)

## `firstLetter()`

Returns the first character of a string, converted to uppercase by default.

The method is Unicode-aware and uses `mbstring` for character extraction and case conversion.

### Signature

```php
StringHelper::firstLetter(string $word, string $format = 'upper'): string
```

### Parameters

* `$word` — The string from which to extract the first character.
* `$format` — The character case. Use `'lower'` for lowercase; any other value results in uppercase.

### Return value

Returns the first character of the string in the requested case.

Returns an empty string when `$word` is empty.

### Examples

```php
use Moudarir\Helpers\StringHelper;

StringHelper::firstLetter('lorem');
// 'L'

StringHelper::firstLetter('Lorem', 'lower');
// 'l'

StringHelper::firstLetter('école');
// 'É'

StringHelper::firstLetter('École', 'lower');
// 'é'

StringHelper::firstLetter('');
// ''
```

---

## `firstLetters()`

Returns the first character of each word in a string.

Words are separated using whitespace, including spaces, tabs, and line breaks. Repeated whitespace is ignored.

The extracted characters are joined using the specified separator.

### Signature

```php
StringHelper::firstLetters(
    string $string,
    string $separator = '.',
    string $format = 'upper'
): string
```

### Parameters

* `$string` — The string containing the words.
* `$separator` — The separator used between extracted characters. Defaults to `'.'`.
* `$format` — The character case. Use `'lower'` for lowercase; any other value results in uppercase.

### Return value

Returns the first character of each word joined by the specified separator.

Returns an empty string when `$string` is empty.

### Examples

```php
use Moudarir\Helpers\StringHelper;

StringHelper::firstLetters('Lorem ipsum dolor');
// 'L.I.D'

StringHelper::firstLetters('Lorem ipsum dolor', '-');
// 'L-I-D'

StringHelper::firstLetters('Lorem ipsum dolor', '.', 'lower');
// 'l.i.d'

StringHelper::firstLetters("  Lorem   ipsum\t dolor\n");
// 'L.I.D'

StringHelper::firstLetters('école àbaco');
// 'É.À'
```

---

## `toCamelcase()`

Converts a separator-delimited string to camelCase.

The specified separator is replaced by spaces, each word is capitalized, and the first character of the resulting string is converted to lowercase.

### Signature

```php
StringHelper::toCamelcase(string $string, string $separator = '_'): string
```

### Parameters

* `$string` — The string to convert.
* `$separator` — The separator used between words. Defaults to `'_'`.

### Return value

Returns the converted string in camelCase.

Returns an empty string when `$string` is empty.

### Examples

```php
use Moudarir\Helpers\StringHelper;

StringHelper::toCamelcase('hello_world');
// 'helloWorld'

StringHelper::toCamelcase('hello-world', '-');
// 'helloWorld'

StringHelper::toCamelcase('this_is_a_string');
// 'thisIsAString'

StringHelper::toCamelcase('HelloWorld');
// 'helloWorld'
```

---

## `bytesToHuman()`

Converts a byte value into a human-readable representation.

The method supports both binary and decimal units.

When `$binary` is `true`, values are divided by 1024 and use IEC units:

```text
B, KiB, MiB, GiB, TiB, PiB
```

When `$binary` is `false`, values are divided by 1000 and use SI units:

```text
B, kB, MB, GB, TB, PB
```

The resulting value is rounded to a maximum of two decimal places.

### Signature

```php
StringHelper::bytesToHuman(int $bytes, bool $binary = true): string
```

### Parameters

* `$bytes` — The number of bytes to convert. Negative values are supported.
* `$binary` — Determines the unit system:

    * `true` — binary units using a base of 1024.
    * `false` — decimal units using a base of 1000.

### Return value

Returns the formatted byte value with its corresponding unit.

The largest supported unit is `PiB` for binary values and `PB` for decimal values.

### Examples

```php
use Moudarir\Helpers\StringHelper;

StringHelper::bytesToHuman(0);
// '0 B'

StringHelper::bytesToHuman(1024);
// '1 KiB'

StringHelper::bytesToHuman(1536);
// '1.5 KiB'

StringHelper::bytesToHuman(1024 ** 2);
// '1 MiB'

StringHelper::bytesToHuman(1000, false);
// '1 kB'

StringHelper::bytesToHuman(1500, false);
// '1.5 kB'

StringHelper::bytesToHuman(-1024);
// '-1 KiB'
```

---

## `excerpt()`

Creates a shortened text excerpt while respecting a maximum character length.

HTML tags are removed before the length is calculated.

When the input contains a `<p>` element, the content of the first `<p>` element is used as the source text before HTML tags are removed.

The ellipsis is included in the maximum length.

The method is Unicode-aware and uses `mbstring` to count and extract characters.

### Signature

```php
StringHelper::excerpt(
    string $string,
    int $maxLength = 160,
    string $ellipsisPosition = 'right',
    string $ellipsis = '…'
): string
```

### Parameters

* `$string` — The source string, which may contain HTML.
* `$maxLength` — The maximum length of the returned string, including the ellipsis.
* `$ellipsisPosition` — Determines where the ellipsis is placed:

    * `'right'` — Places the ellipsis at the end of the excerpt.
    * `'left'` — Places the ellipsis at the beginning of the excerpt.
    * Any other value defaults to `'right'`.
* `$ellipsis` — The string used as the ellipsis. Defaults to the Unicode ellipsis character `…`.

### Return value

Returns the excerpt.

Returns an empty string when:

* `$string` is empty;
* the resulting text contains no content after HTML stripping;
* `$maxLength` is zero or negative.

If the source string does not exceed `$maxLength`, it is returned without an ellipsis.

If the ellipsis is longer than or equal to `$maxLength`, it is truncated to `$maxLength` characters.

### Examples

```php
use Moudarir\Helpers\StringHelper;

StringHelper::excerpt('Lorem ipsum dolor', 11);
// 'Lorem ipsu…'

StringHelper::excerpt('Lorem ipsum dolor', 8, 'left');
// '…m dolor'

StringHelper::excerpt('Lorem ipsum dolor', 11, 'unknown');
// 'Lorem ipsu…'

StringHelper::excerpt('Lorem ipsum', 11);
// 'Lorem ipsum'

StringHelper::excerpt(
    '<strong>Lorem</strong> <em>ipsum</em>'
);
// 'Lorem ipsum'

StringHelper::excerpt(
    '<p>Lorem ipsum</p><p>Dolor sit amet</p>'
);
// 'Lorem ipsum'

StringHelper::excerpt(
    'Été très chaud',
    4
);
// 'Été…'
```

### Custom ellipsis

A custom ellipsis can be supplied as the fourth argument. Its length is included in `$maxLength`.

```php
StringHelper::excerpt(
    'Lorem ipsum dolor',
    10,
    'right',
    '... '
);
// 'Lorem ... '
```

---

## Requirements

`StringHelper` requires the PHP `mbstring` extension for Unicode-aware string operations.
