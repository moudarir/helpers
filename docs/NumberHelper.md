# NumberHelper

`NumberHelper` provides lightweight helpers for number formatting, percentage calculations, integer code generation, and ceiling values at a specified precision.

## Available Methods

* [`format()`](#format)
* [`percent()`](#percent)
* [`generateIntegerCode()`](#generateintegercode)
* [`ceiling()`](#ceiling)

---

## `format()`

```php
public static function format(
    float $number,
    int $decimals = 0,
    string $decPoint = ',',
    string $thousandsSep = ' '
): string
```

Formats a number using PHP's `number_format()` function.

By default, numbers use a comma as the decimal separator and a space as the thousands separator.

### Parameters

| Parameter       | Type     | Default | Description              |
| --------------- | -------- | ------- | ------------------------ |
| `$number`       | `float`  | —       | Number to format         |
| `$decimals`     | `int`    | `0`     | Number of decimal places |
| `$decPoint`     | `string` | `,`     | Decimal separator        |
| `$thousandsSep` | `string` | ` `     | Thousands separator      |

### Return Value

Returns the formatted number as a string.

### Example

```php
$formatted = NumberHelper::format(1234567.89, 2);
```

Result:

```text
1 234 567,89
```

Custom separators can be provided:

```php
$formatted = NumberHelper::format(1234567.89, 2, '.', ',');
```

Result:

```text
1,234,567.89
```

---

## `percent()`

```php
public static function percent(
    int $valeur,
    int $total,
    int $precision = 2
): float
```

Calculates the percentage represented by `$valeur` relative to `$total`.

The result is rounded to the number of decimal places specified by `$precision`.

When `$total` is `0`, the method returns `0.0`.

### Parameters

| Parameter    | Type  | Default | Description                            |
| ------------ | ----- | ------- | -------------------------------------- |
| `$valeur`    | `int` | —       | Value used to calculate the percentage |
| `$total`     | `int` | —       | Total value                            |
| `$precision` | `int` | `2`     | Number of decimal places               |

### Return Value

Returns the calculated percentage as a `float`.

### Example

```php
$percentage = NumberHelper::percent(25, 200);
```

Result:

```text
12.5
```

A custom precision can be specified:

```php
$percentage = NumberHelper::percent(1, 3, 4);
```

Result:

```text
33.3333
```

---

## `generateIntegerCode()`

```php
/**
 * @throws RandomException
 */
public static function generateIntegerCode(int $length = 6): int
```

Generates a cryptographically secure random integer code with the specified number of digits.

The method uses PHP's `random_int()` function.

The default length is `6`, producing a value between `100000` and `999999`.

A length less than or equal to `0` returns `0`.

### Parameters

| Parameter | Type  | Default | Description      |
| --------- | ----- | ------- | ---------------- |
| `$length` | `int` | `6`     | Number of digits |

### Return Value

Returns the generated integer code.

Returns `0` when `$length` is less than or equal to `0`.

### Exceptions

Throws `Random\RandomException` when a cryptographically secure random integer cannot be generated.

### Example

```php
$code = NumberHelper::generateIntegerCode();
```

A specific number of digits can be requested:

```php
$code = NumberHelper::generateIntegerCode(4);
```

The resulting value is between `1000` and `9999`.

---

## `ceiling()`

```php
public static function ceiling(
    float $number,
    int $placement = 1
): float
```

Rounds a number upward according to the requested placement.

The `$placement` value is normalized to a minimum of `1`.

The method first rounds the number according to its internal precision, then applies `ceil()` and returns the resulting value as a `float`.

### Parameters

| Parameter    | Type    | Default | Description                              |
| ------------ | ------- | ------- | ---------------------------------------- |
| `$number`    | `float` | —       | Number to round                          |
| `$placement` | `int`   | `1`     | Decimal placement used for the operation |

### Return Value

Returns the resulting value as a `float`.

Values of `$placement` less than `1` are treated as `1`.

### Examples

```php
$value = NumberHelper::ceiling(1.251, 2);
```

Result:

```text
1.3
```

The default placement is `1`:

```php
$value = NumberHelper::ceiling(1.1);
```

Result:

```text
1.0
```

A placement less than `1` is normalized:

```php
$value = NumberHelper::ceiling(1.1, 0);
```

Result:

```text
1.0
```
