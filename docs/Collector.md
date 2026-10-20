# Collector

`Collector` provides a set of static utility methods for working with arrays and iterables. It simplifies common data manipulation tasks through a consistent API for transforming, filtering, grouping, sorting, and extracting values.

Most methods accept arrays, while methods such as `keyBy()`, `collapse()`, `flatten()`, and `pluck()` also accept iterables where applicable.

## Available Methods

* [`map()`](#map)
* [`mapWithKeys()`](#mapwithkeys)
* [`filter()`](#filter)
* [`reject()`](#reject)
* [`partition()`](#partition)
* [`chunk()`](#chunk)
* [`groupBy()`](#groupby)
* [`keyBy()`](#keyby)
* [`sortBy()`](#sortby)
* [`sortByDesc()`](#sortbydesc)
* [`sortKeys()`](#sortkeys)
* [`sortKeysDesc()`](#sortkeysdesc)
* [`unique()`](#unique)
* [`uniqueStrict()`](#uniquestrict)
* [`min()`](#min)
* [`max()`](#max)
* [`collapse()`](#collapse)
* [`flatten()`](#flatten)
* [`random()`](#random)
* [`pop()`](#pop)
* [`first()`](#first)
* [`last()`](#last)
* [`implode()`](#implode)
* [`join()`](#join)
* [`pluck()`](#pluck)
* [`every()`](#every)
* [`flip()`](#flip)
* [`where()`](#where)
* [`randomly()`](#randomly)

## `map()`

```php
public static function map(array $items, callable $callback): array
```

Applies a callback to each item and returns the transformed array. The callback receives the item value and its key.

Original keys are preserved.

### Parameters

| Parameter   | Type       | Description                          |
|-------------|------------|--------------------------------------|
| `$items`    | `array`    | The input array                      |
| `$callback` | `callable` | Callback receiving the value and key |

### Example

```php
$result = Collector::map(
    ['a' => 1, 'b' => 2],
    static fn (int $value, string $key): string => $key . ':' . $value
);

// ['a' => 'a:1', 'b' => 'b:2']
```

## `mapWithKeys()`

```php
public static function mapWithKeys(
    array $items,
    callable $callback
): array
```

Transforms items into an associative array. The callback receives the item value and its original key and must return an array of key/value pairs. When a key is returned more than once, the last value wins.

### Parameters

| Parameter   | Type       | Description                         |
|-------------|------------|-------------------------------------|
| `$items`    | `array`    | The input array                     |
| `$callback` | `callable` | Returns one or more key/value pairs |

### Example

```php
$result = Collector::mapWithKeys(
    ['first' => 'Alice', 'second' => 'Bob'],
    static fn (string $name, string $key): array => [
        $name => $key,
    ]
);

// ['Alice' => 'first', 'Bob' => 'second']
```

## `filter()`

```php
public static function filter(
    array $items,
    ?callable $callback = null
): array
```

Filters items according to a callback. When no callback is supplied, PHP's default truthiness filtering is used. Original keys are preserved.

### Parameters

| Parameter   | Type        | Description                                    |
|-------------|-------------|------------------------------------------------|
| `$items`    | `array`     | The input array                                |
| `$callback` | `?callable` | Optional predicate receiving the value and key |

### Example

```php
$result = Collector::filter(
    ['a' => 0, 'b' => 2, 'c' => 3],
    static fn (int $value): bool => $value > 1
);

// ['b' => 2, 'c' => 3]
```

## `reject()`

```php
public static function reject(
    array $items,
    $callback = true
): array
```

Returns items that do not satisfy the supplied condition. A callable is treated as a predicate; otherwise, items are compared with the provided value using loose comparison.

Original keys are preserved.

### Parameters

| Parameter   | Type    | Description                  |
|-------------|---------|------------------------------|
| `$items`    | `array` | The input array              |
| `$callback` | `mixed` | Predicate or value to reject |

### Example

```php
$result = Collector::reject(
    [1, 2, 3, 4],
    static fn (int $value): bool => $value % 2 === 0
);

// [0 => 1, 2 => 3]
```

## `partition()`

```php
public static function partition(
    array $items,
    callable $predicate
): array
```

Splits items into two arrays according to a predicate. The first array contains matching items; the second contains non-matching items.

Original keys are preserved in both arrays.

### Parameters

| Parameter    | Type       | Description                           |
|--------------|------------|---------------------------------------|
| `$items`     | `array`    | The input array                       |
| `$predicate` | `callable` | Predicate receiving the value and key |

### Example

```php
[$even, $odd] = Collector::partition(
    [1, 2, 3, 4],
    static fn (int $value): bool => $value % 2 === 0
);

// $even = [1 => 2, 3 => 4]
// $odd = [0 => 1, 2 => 3]
```

## `chunk()`

```php
public static function chunk(array $items, int $length): array
```

Splits an array into chunks of the specified length while preserving original keys within each chunk.

When `$length` is less than or equal to zero, the original array is returned unchanged.

### Parameters

| Parameter | Type    | Description                       |
|-----------|---------|-----------------------------------|
| `$items`  | `array` | The input array                   |
| `$length` | `int`   | Maximum number of items per chunk |

### Example

```php
$result = Collector::chunk([10, 20, 30, 40, 50], 2);

// [[10, 20], [30, 40], [50]]
```

## `groupBy()`

```php
public static function groupBy(
    array $items,
    callable|string $groupBy
): array
```

Groups items by an array key, object property, or callback result. The callback receives the item value and original key.

A missing grouping value is grouped under the empty-string key. Items within each group retain their original values and order.

### Parameters

| Parameter  | Type               | Description                   |
|------------|--------------------|-------------------------------|
| `$items`   | `array`            | The input array               |
| `$groupBy` | `callable\|string` | Callback or key/property name |

### Example

```php
$result = Collector::groupBy(
    [
        ['name' => 'Alice', 'role' => 'admin'],
        ['name' => 'Bob', 'role' => 'user'],
        ['name' => 'Eve', 'role' => 'admin'],
    ],
    'role'
);

// [
//     'admin' => [...Alice, ...Eve],
//     'user' => [...Bob],
// ]
```

## `keyBy()`

```php
public static function keyBy(
    iterable $items,
    callable|string $keyBy
): array
```

Re-indexes items using an array key, object property, or callback result. The callback receives the item value and original key.

If multiple items produce the same key, the last item overwrites the previous value. A missing key is represented by the empty string.

### Parameters

| Parameter | Type               | Description                   |
|-----------|--------------------|-------------------------------|
| `$items`  | `iterable`         | The input iterable            |
| `$keyBy`  | `callable\|string` | Callback or key/property name |

### Example

```php
$result = Collector::keyBy(
    [
        ['id' => 10, 'name' => 'Alice'],
        ['id' => 20, 'name' => 'Bob'],
    ],
    'id'
);

// [
//     10 => ['id' => 10, 'name' => 'Alice'],
//     20 => ['id' => 20, 'name' => 'Bob'],
// ]
```

## `sortBy()`

```php
public static function sortBy(
    array $items,
    callable|string|null $callback = null,
    bool $descending = false,
    bool $preserveKeys = false
): array
```

Sorts items by their values or by values extracted using a callback or key/property name. The callback may return an array of criteria for multi-criteria sorting.

When `$callback` is `null`, native PHP sorting functions are used. Keys are re-indexed by default when sorting with a callback and `$preserveKeys` is `false`.

### Parameters

| Parameter       | Type                     | Description                          |
|-----------------|--------------------------|--------------------------------------|
| `$items`        | `array`                  | The input array                      |
| `$callback`     | `callable\|string\|null` | Sorting criterion                    |
| `$descending`   | `bool`                   | Sort in descending order when `true` |
| `$preserveKeys` | `bool`                   | Preserve original keys when `true`   |

### Example

```php
$result = Collector::sortBy(
    [
        ['name' => 'Charlie'],
        ['name' => 'Alice'],
        ['name' => 'Bob'],
    ],
    'name'
);

// Alice, Bob, Charlie
```

## `sortByDesc()`

```php
public static function sortByDesc(
    array $items,
    callable|string|null $callback = null,
    bool $preserveKeys = false
): array
```

Sorts items in descending order by value, key/property, or callback. It delegates to `sortBy()` with descending sorting enabled.

### Parameters

| Parameter       | Type                     | Description                        |
|-----------------|--------------------------|------------------------------------|
| `$items`        | `array`                  | The input array                    |
| `$callback`     | `callable\|string\|null` | Sorting criterion                  |
| `$preserveKeys` | `bool`                   | Preserve original keys when `true` |

### Example

```php
$result = Collector::sortByDesc(
    [['score' => 10], ['score' => 30], ['score' => 20]],
    'score'
);

// Scores: 30, 20, 10
```

## `sortKeys()`

```php
public static function sortKeys(
    array $items,
    int $flags = SORT_REGULAR,
    bool $descending = false
): array
```

Sorts an array by its keys. Original keys and values are preserved.

### Parameters

| Parameter     | Type    | Description                                        |
|---------------|---------|----------------------------------------------------|
| `$items`      | `array` | The input array                                    |
| `$flags`      | `int`   | Sorting flags accepted by `ksort()` and `krsort()` |
| `$descending` | `bool`  | Sort keys in descending order when `true`          |

### Example

```php
$result = Collector::sortKeys(['c' => 3, 'a' => 1, 'b' => 2]);

// ['a' => 1, 'b' => 2, 'c' => 3]
```

## `sortKeysDesc()`

```php
public static function sortKeysDesc(
    array $items,
    int $flags = SORT_REGULAR
): array
```

Sorts array keys in descending order. Original keys and values are preserved.

### Parameters

| Parameter | Type    | Description                          |
|-----------|---------|--------------------------------------|
| `$items`  | `array` | The input array                      |
| `$flags`  | `int`   | Sorting flags accepted by `krsort()` |

### Example

```php
$result = Collector::sortKeysDesc(['a' => 1, 'c' => 3, 'b' => 2]);

// ['c' => 3, 'b' => 2, 'a' => 1]
```

## `unique()`

```php
public static function unique(
    array $items,
    callable|string|null $key = null,
    bool $strict = false,
    bool $preserveKeys = true
): array
```

Removes duplicate items. Uniqueness can be determined from the items themselves or from values extracted using a key, property, or callback.

When `$strict` is `true`, values are compared strictly. Original keys are preserved by default.

### Parameters

| Parameter       | Type                     | Description                              |
|-----------------|--------------------------|------------------------------------------|
| `$items`        | `array`                  | The input array                          |
| `$key`          | `callable\|string\|null` | Optional uniqueness criterion            |
| `$strict`       | `bool`                   | Use strict value comparisons when `true` |
| `$preserveKeys` | `bool`                   | Preserve original keys when `true`       |

### Example

```php
$result = Collector::unique([1, '1', 1], strict: true);

// [0 => 1, 1 => '1']
```

## `uniqueStrict()`

```php
public static function uniqueStrict(
    array $items,
    callable|string|null $key = null,
    bool $preserveKeys = true
): array
```

Removes duplicate items using strict comparisons. This is equivalent to calling `unique()` with `$strict` set to `true`.

### Parameters

| Parameter       | Type                     | Description                        |
|-----------------|--------------------------|------------------------------------|
| `$items`        | `array`                  | The input array                    |
| `$key`          | `callable\|string\|null` | Optional uniqueness criterion      |
| `$preserveKeys` | `bool`                   | Preserve original keys when `true` |

### Example

```php
$result = Collector::uniqueStrict([1, '1', 1]);

// [0 => 1, 1 => '1']
```

## `min()`

```php
public static function min(
    array $items,
    callable|string|null $key = null
): mixed
```

Returns the minimum value in an array or the minimum non-null value extracted using a key, property, or callback.

Without a criterion, it delegates to PHP's native `min()`. When a criterion is supplied, missing and null extracted values are ignored.

Returns `null` for an empty array or when no non-null value is found with a criterion.

### Parameters

| Parameter | Type                     | Description                   |
|-----------|--------------------------|-------------------------------|
| `$items`  | `array`                  | The input array               |
| `$key`    | `callable\|string\|null` | Optional extraction criterion |

### Example

```php
$result = Collector::min(
    [
        ['score' => 30],
        ['score' => 10],
        ['score' => 20],
    ],
    'score'
);

// 10
```

## `max()`

```php
public static function max(
    array $items,
    callable|string|null $key = null
): mixed
```

Returns the maximum value in an array or the maximum non-null value extracted using a key, property, or callback.

Without a criterion, it delegates to PHP's native `max()`. When a criterion is supplied, missing and null extracted values are ignored.

Returns `null` for an empty array or when no non-null value is found with a criterion.

### Parameters

| Parameter | Type                     | Description                   |
|-----------|--------------------------|-------------------------------|
| `$items`  | `array`                  | The input array               |
| `$key`    | `callable\|string\|null` | Optional extraction criterion |

### Example

```php
$result = Collector::max(
    [
        ['score' => 30],
        ['score' => 10],
        ['score' => 20],
    ],
    'score'
);

// 30
```

## `collapse()`

```php
public static function collapse(iterable $items): array
```

Flattens one level of nested arrays or traversables. Items that are neither arrays nor traversables are ignored. Numeric keys are re-indexed by PHP's `array_merge()`.

### Parameters

| Parameter | Type       | Description                                   |
|-----------|------------|-----------------------------------------------|
| `$items`  | `iterable` | An iterable containing arrays or traversables |

### Example

```php
$result = Collector::collapse([
    [1, 2],
    [3, 4],
    new ArrayIterator([5, 6])
]);

// [1, 2, 3, 4, 5, 6]
```

## `flatten()`

```php
public static function flatten(
    iterable $items,
    int|float $depth = INF
): array
```

Flattens nested arrays and traversables into a single-level array or to the specified depth. Non-array items are retained as values. The returned array is a list with sequential numeric keys.

A depth of `1` flattens one level. The default `INF` recursively flattens all nested levels.

### Parameters

| Parameter | Type         | Description                                 |
|-----------|--------------|---------------------------------------------|
| `$items`  | `iterable`   | The input iterable                          |
| `$depth`  | `int\|float` | Maximum flattening depth; defaults to `INF` |

### Example

```php
$result = Collector::flatten([1, [2, [3, 4]]], 1);

// [1, 2, [3, 4]]
```

## `random()`

```php
public static function random(
    array $items,
    ?int $number = null,
    bool $preserveKeys = false
): mixed
```

Returns one randomly selected item when `$number` is `null`, or an array containing the requested number of randomly selected items.

When selecting multiple items, keys are re-indexed by default. If `$preserveKeys` is `true`, the original keys are retained.

### Parameters

| Parameter       | Type    | Description                                        |
|-----------------|---------|----------------------------------------------------|
| `$items`        | `array` | The input array                                    |
| `$number`       | `?int`  | Number of items to select; `null` selects one item |
| `$preserveKeys` | `bool`  | Preserve keys for multiple selections              |

### Example

```php
$one = Collector::random(['a', 'b', 'c']);

$many = Collector::random(['a', 'b', 'c'], 2);
```

### Exceptions

Throws `InvalidArgumentException` when the requested number exceeds the number of available items.

## `pop()`

```php
public static function pop(
    array &$items,
    int $numToGet = 1
): mixed
```

Removes and returns the last item or the last requested number of items from the input array. The input array is modified.

When one item is requested, the item is returned directly. When multiple items are requested, they are returned in their original relative order. A request larger than the array returns all remaining items.

Returns `null` when the array is empty or `$numToGet` is less than `1`.

### Parameters

| Parameter   | Type    | Description               |
|-------------|---------|---------------------------|
| `$items`    | `array` | The array to modify       |
| `$numToGet` | `int`   | Number of items to remove |

### Example

```php
$items = ['a', 'b', 'c', 'd'];

$result = Collector::pop($items, 2);

// $result = ['c', 'd']
// $items = ['a', 'b']
```

## `first()`

```php
public static function first(
    array $items,
    ?callable $callback = null,
    mixed $default = null
): mixed
```

Returns the first item in the array or the first item matching a predicate. When no item matches, the default value is returned.

If the default is callable, it is invoked only when needed.

### Parameters

| Parameter   | Type        | Description                                     |
|-------------|-------------|-------------------------------------------------|
| `$items`    | `array`     | The input array                                 |
| `$callback` | `?callable` | Optional predicate receiving value and key      |
| `$default`  | `mixed`     | Fallback value or callable returning a fallback |

### Example

```php
$result = Collector::first(
    [1, 3, 4, 6],
    static fn (int $value): bool => $value % 2 === 0
);

// 4
```

## `last()`

```php
public static function last(
    array $items,
    ?callable $callback = null,
    mixed $default = null
): mixed
```

Returns the last item in the array or the last item matching a predicate. When no item matches, the default value is returned.

If the default is callable, it is invoked only when needed.

### Parameters

| Parameter   | Type        | Description                                     |
|-------------|-------------|-------------------------------------------------|
| `$items`    | `array`     | The input array                                 |
| `$callback` | `?callable` | Optional predicate receiving value and key      |
| `$default`  | `mixed`     | Fallback value or callable returning a fallback |

### Example

```php
$result = Collector::last(
    [1, 2, 3, 4, 5],
    static fn (int $value): bool => $value % 2 === 0
);

// 4
```

## `implode()`

```php
public static function implode(
    array $items,
    callable|string|null $value = null,
    ?string $glue = null
): string
```

Joins array items into a string.

When `$glue` is `null` and `$value` is not treated as a callback, `$value` is used as the separator, like PHP's native `implode()`.

When `$glue` is supplied, `$value` can specify a key, object property, or callback used to extract each item's text. Missing keys or properties produce an empty string.

### Parameters

| Parameter | Type                     | Description                             |
|-----------|--------------------------|-----------------------------------------|
| `$items`  | `array`                  | The input array                         |
| `$value`  | `callable\|string\|null` | Separator or value extraction criterion |
| `$glue`   | `?string`                | Optional separator for extracted values |

### Example

```php
$result = Collector::implode(
    [
        ['name' => 'Alice'],
        ['name' => 'Bob'],
    ],
    'name',
    ', '
);

// 'Alice, Bob'
```

## `join()`

```php
public static function join(
    array $items,
    string $glue = ', ',
    string $finalGlue = ''
): string
```

Joins array items into a string with a separator between regular items and an optional final separator before the last item.

When `$finalGlue` is empty, the method behaves like native `implode()` using `$glue`.

### Parameters

| Parameter    | Type     | Description                     |
|--------------|----------|---------------------------------|
| `$items`     | `array`  | The input array                 |
| `$glue`      | `string` | Separator between regular items |
| `$finalGlue` | `string` | Separator before the last item  |

### Example

```php
$result = Collector::join(['Alice', 'Bob', 'Charlie'], ', ', ' and ');

// 'Alice, Bob and Charlie'
```

## `pluck()`

```php
public static function pluck(
    iterable $items,
    string|int|null $value,
    ?string $key = null
): array
```

Extracts values from an iterable of arrays or objects. If `$value` is `null`, the original items are returned.

When `$key` is supplied, it determines the keys in the resulting array. Missing values are represented by `null`. Items with missing or invalid result keys are skipped when `$key` is provided. Duplicate result keys overwrite previous values.

Objects implementing `Stringable` can be converted to string keys.

### Parameters

| Parameter | Type                | Description                                     |
|-----------|---------------------|-------------------------------------------------|
| `$items`  | `iterable`          | The input iterable                              |
| `$value`  | `string\|int\|null` | Key/property to extract, or `null` for the item |
| `$key`    | `?string`           | Optional key/property used for the result keys  |

### Example

```php
$result = Collector::pluck(
    [
        ['id' => 10, 'name' => 'Alice'],
        ['id' => 20, 'name' => 'Bob'],
    ],
    'name',
    'id'
);

// [10 => 'Alice', 20 => 'Bob']
```

## `every()`

```php
public static function every(
    array $items,
    callable|string|null $callback = null
): bool
```

Checks whether all items satisfy a condition. Without a callback, all values must be truthy. A callback receives the item value and key; a string can identify an array key or object property.

An empty array returns `true`.

### Parameters

| Parameter   | Type                     | Description                             |
|-------------|--------------------------|-----------------------------------------|
| `$items`    | `array`                  | The input array                         |
| `$callback` | `callable\|string\|null` | Optional predicate or key/property name |

### Example

```php
$result = Collector::every(
    [
        ['active' => true],
        ['active' => true],
    ],
    'active'
);

// true
```

## `flip()`

```php
public static function flip(array $items): array
```

Exchanges array keys with their corresponding values, using PHP's native `array_flip()` behavior.

Values must be integers or strings to be used as keys. Duplicate values overwrite earlier entries.

### Parameters

| Parameter | Type    | Description     |
|-----------|---------|-----------------|
| `$items`  | `array` | The input array |

### Example

```php
$result = Collector::flip(['first' => 'a', 'second' => 'b']);

// ['a' => 'first', 'b' => 'second']
```

## `where()`

```php
public static function where(
    array $items,
    callable $callback
): array
```

Filters items using a callback that receives each value and key. Original keys are preserved.

### Parameters

| Parameter   | Type       | Description                           |
|-------------|------------|---------------------------------------|
| `$items`    | `array`    | The input array                       |
| `$callback` | `callable` | Predicate receiving the value and key |

### Example

```php
$result = Collector::where(
    ['a' => 1, 'b' => 2, 'c' => 3],
    static fn (int $value, string $key): bool => $key !== 'b' && $value > 1
);

// ['c' => 3]
```

## `randomly()`

```php
public static function randomly(
    array $array,
    ?int $number = null,
    bool $preserveKeys = false
): mixed
```

Selects a random item or a specified number of random items. When `$number` is `null`, a single item is returned. Otherwise, an array is returned.

An empty array returns `null` when no number is requested and returns an empty array when the requested number is less than or equal to zero.
When a positive number larger than the available item count is requested, `InvalidArgumentException` is thrown.

### Parameters

| Parameter       | Type    | Description                                        |
|-----------------|---------|----------------------------------------------------|
| `$array`        | `array` | The input array                                    |
| `$number`       | `?int`  | Number of items to select; `null` selects one item |
| `$preserveKeys` | `bool`  | Preserve original keys for multiple selections     |

### Example

```php
$one = Collector::randomly(['a', 'b', 'c']);

$many = Collector::randomly(['a', 'b', 'c'], 2, true);
```

### Exceptions

Throws `InvalidArgumentException` when the requested number exceeds the number of available items.
