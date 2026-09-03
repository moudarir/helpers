# Array Helper

`ArrayHelper` provides lightweight utility methods for common array operations. It offers methods for extracting values from arrays or objects, converting values to integers or strings, extracting image sources from HTML content, and comparing arrays.

## Available Methods

- [`diff()`](#diff)
- [`ids()`](#ids)
- [`toInt()`](#toint)
- [`toString()`](#tostring)
- [`extractImgSrc()`](#extractimgsrc)

### `diff()`

Compares two arrays and returns their differences.

The `default` array represents the existing values, while the `new` array represents the new values.

```php
$result = ArrayHelper::diff([1, 2, 3], [2, 3, 4]);
```

The result contains three keys:

```php
[
    'diff' => true,
    'add' => [4],
    'remove' => [1],
]
```

`diff` is `true` when values have been added or removed.

When the arrays contain the same values:

```php
$result = ArrayHelper::diff([1, 2, 3], [1, 2, 3]);

// [
//     'diff' => false,
//     'add' => [],
//     'remove' => [],
// ]
```

Set `$asIds` to `true` to convert values from the `$new` array to integers before comparing:

```php
$result = ArrayHelper::diff([1, 2, 3], ['2', '3', '4'], true);

// [
//     'diff' => true,
//     'add' => [4],
//     'remove' => [1],
// ]
```

### `ids()`

Extracts values from a specific `key` of arrays or objects.

```php
use Moudarir\Helpers\ArrayHelper;

$rows = [
    ['id' => 10, 'name' => 'John'],
    ['id' => 20, 'name' => 'Jane'],
];

$ids = ArrayHelper::ids($rows);

// [10, 20]
```

The `key` can be customized:

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

### `toInt()`

Converts valid integer values to integers.

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

Integer strings must use the decimal representation without leading zeros or surrounding whitespace.

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

### `toString()`

Converts values to strings.

```php
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

> If `$rejectEmpty` is set to `false`, the `null` values are converted to empty strings `''`.

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

### `extractImgSrc()`

Extracts the `src` attributes from HTML `<img>` elements.

```php
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

An empty array is returned when no image source is found.
