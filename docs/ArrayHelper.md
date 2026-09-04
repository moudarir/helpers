# Array Helper

`ArrayHelper` provides lightweight utility methods for common array operations. It offers methods for extracting values from arrays or objects, converting values to integers or strings, extracting image sources from HTML content, and comparing arrays.

## Available Methods

* [`diff()`](#diff)
* [`ids()`](#ids)
* [`toInt()`](#toint)
* [`toString()`](#tostring)
* [`extractImgSrc()`](#extractimgsrc)

## `diff()`

Compares two arrays and returns their differences.

The `$default` array represents the existing values, while the `$new` array represents the new values.

### Signature

```php
ArrayHelper::diff(
    array $default,
    array $new,
    bool $asIds = false
): array
```

### Parameters

* `$default` — The existing values.
* `$new` — The new values.
* `$asIds` — When `true`, values from `$new` are converted to integers before comparison.

### Return value

Returns an array containing:

* `diff` — `true` when values have been added or removed, otherwise `false`.
* `add` — Values present in `$new` but not in `$default`.
* `remove` — Values present in `$default` but not in `$new`.

### Examples

```php
use Moudarir\Helpers\ArrayHelper;

$result = ArrayHelper::diff([1, 2, 3], [2, 3, 4]);

// [
//     'diff' => true,
//     'add' => [4],
//     'remove' => [1],
// ]
```

When both arrays contain the same values:

```php
$result = ArrayHelper::diff([1, 2, 3], [1, 2, 3]);

// [
//     'diff' => false,
//     'add' => [],
//     'remove' => [],
// ]
```

Set `$asIds` to `true` to convert values from `$new` to integers before comparing:

```php
$result = ArrayHelper::diff([1, 2, 3], ['2', '3', '4'], true);

// [
//     'diff' => true,
//     'add' => [4],
//     'remove' => [1],
// ]
```

The returned arrays preserve the keys produced by the underlying array comparison.

---

## `ids()`

Extracts values associated with a specified key from an array of arrays or objects.

Missing keys and `null` values are ignored.

### Signature

```php
ArrayHelper::ids(
    array $items,
    string $key = 'id'
): array
```

### Parameters

* `$items` — An array containing arrays or objects.
* `$key` — The key or property to extract. Defaults to `'id'`.

### Return value

Returns the extracted values as a reindexed list.

### Examples

```php
use Moudarir\Helpers\ArrayHelper;

$rows = [
    ['id' => 10, 'name' => 'John'],
    ['id' => 20, 'name' => 'Jane'],
];

$ids = ArrayHelper::ids($rows);

// [10, 20]
```

The key can be customized:

```php
$rows = [
    ['uuid' => 'abc'],
    ['uuid' => 'def'],
];

$ids = ArrayHelper::ids($rows, 'uuid');

// ['abc', 'def']
```

Missing or `null` values are ignored:

```php
$rows = [
    ['id' => 10],
    ['name' => 'John'],
    ['id' => null],
    ['id' => 20],
];

$ids = ArrayHelper::ids($rows);

// [10, 20]
```

The method also supports objects:

```php
$rows = [
    (object) ['id' => 10],
    (object) ['id' => 20],
];

$ids = ArrayHelper::ids($rows);

// [10, 20]
```

---

## `toInt()`

Converts valid integer values and integer strings to integers.

By default, only positive integers are accepted.

### Signature

```php
ArrayHelper::toInt(
    array|int|string|null $values,
    bool $onlyPositive = true,
    bool $unique = false
): array
```

### Parameters

* `$values` — An array, integer, string, or `null`.
* `$onlyPositive` — When `true`, only positive integers are accepted. Defaults to `true`.
* `$unique` — When `true`, duplicate integer values are removed.

### Return value

Returns an array containing the valid integer values.

Invalid values are ignored.

`null` returns an empty array.

### Examples

By default, only positive integers are accepted:

```php
$values = ArrayHelper::toInt([123, '456', 0, -10]);

// [123, 456]
```

Set `$onlyPositive` to `false` to also accept zero and negative integers:

```php
$values = ArrayHelper::toInt([123, '456', 0, -10, '-20'], false);

// [123, 456, 0, -10, -20]
```

Integer strings must use decimal notation without leading zeros, a leading plus sign, or surrounding whitespace.

For example:

```php
$values = ArrayHelper::toInt(['123', '-456', '0', '00', '00123', '+123', '12.5', '1e3', ' 123']);

// [123]
```

The `$unique` option removes duplicate values:

```php
$values = ArrayHelper::toInt([123, '123', 456, '456'], unique: true);

// [123, 456]
```

A scalar value can also be provided:

```php
$value = ArrayHelper::toInt('123');

// [123]
```

`null` returns an empty array:

```php
$value = ArrayHelper::toInt(null);

// []
```

---

## `toString()`

Converts integer and string values to strings.

### Signature

```php
ArrayHelper::toString(
    array|int|string|null $values,
    bool $rejectEmpty = true,
    bool $unique = false
): array
```

### Parameters

* `$values` — An array, integer, string, or `null`.
* `$rejectEmpty` — When `true`, empty values are rejected. Defaults to `true`.
* `$unique` — When `true`, duplicate string values are removed.

### Return value

Returns an array containing the converted string values.

Invalid or rejected values are ignored.

`null` returns an empty array.

### Examples

```php
use Moudarir\Helpers\ArrayHelper;

$values = ArrayHelper::toString([123, 456]);

// ['123', '456']
```

By default, empty values are rejected:

```php
$values = ArrayHelper::toString(['', 'foo', null, 'bar']);

// ['foo', 'bar']
```

Set `$rejectEmpty` to `false` to preserve empty values:

```php
$values = ArrayHelper::toString(['', 'foo', null, 'bar'], false);

// ['', 'foo', '', 'bar']
```

> When `$rejectEmpty` is `false`, `null` values are converted to empty strings `''`.

The `$unique` option removes duplicate values:

```php
$values = ArrayHelper::toString([123, '123', 456, '456'], unique: true);

// ['123', '456']
```

A scalar value can also be provided:

```php
$value = ArrayHelper::toString(123);

// ['123']
```

`null` returns an empty array:

```php
$value = ArrayHelper::toString(null);

// []
```

---

## `extractImgSrc()`

Extracts the `src` attributes from HTML `<img>` elements.

The search is case-insensitive and supports both single and double quotes.

### Signature

```php
ArrayHelper::extractImgSrc(string $html): array
```

### Parameters

* `$html` — The HTML content to search.

### Return value

Returns an array containing the extracted `src` attribute values.

An empty array is returned when no image source is found.

### Examples

```php
use Moudarir\Helpers\ArrayHelper;

$content = <<<'HTML'
<img src="image-1.jpg">
<img src="image-2.png">
HTML;

$sources = ArrayHelper::extractImgSrc($content);

// ['image-1.jpg', 'image-2.png']
```

Both single and double quotes are supported:

```php
$content = <<<'HTML'
<img src="image-1.jpg">
<img src='image-2.png'>
HTML;

$sources = ArrayHelper::extractImgSrc($content);

// ['image-1.jpg', 'image-2.png']
```

The method is case-insensitive:

```php
$content = <<<'HTML'
<IMG SRC="image-1.jpg">
<Img Src="image-2.png">
HTML;

$sources = ArrayHelper::extractImgSrc($content);

// ['image-1.jpg', 'image-2.png']
```

When no image source is found:

```php
$sources = ArrayHelper::extractImgSrc('<p>Lorem ipsum</p>');

// []
```
