# DatetimeHelper

`DatetimeHelper` extends PHP's native `DateTimeImmutable` class and provides additional utilities for date parsing, timezone configuration, localized formatting, date differences, and duration calculations.

Features include:

- Date creation, parsing, and immutable modification.
- Timezone and locale configuration.
- Localized date formatting through ICU.
- Relative date descriptions with locale-aware plural rules.
- Elapsed duration formatting.
- Language files for French, English, and Arabic.
- Automatic language selection based on the configured locale, with French as the fallback language.

It uses PHP's native date and time functionality without requiring Carbon.

## Available Methods

### Configuration

- [`timeZone()`](#timezone)
- [`changeTimeZone()`](#changetimezone)
- [`setLocale()`](#setlocale)

### Date Creation and Parsing

- [`now()`](#now)
- [`parseFromFormat()`](#parsefromformat)
- [`getFromTimestamp()`](#getfromtimestamp)
- [`getFromFormat()`](#getfromformat)

### Date Modification

`DatetimeHelper` provides methods for shifting dates and setting the boundaries of days, months, and years.

Since `DatetimeHelper` extends `DateTimeImmutable`, these methods return a new instance without modifying the original object.

- [`modify()`](#modify)
- [`addHours()`](#addhours)
- [`subHours()`](#subhours)
- [`addDays()`](#adddays)
- [`subDays()`](#subdays)
- [`addMonths()`](#addmonths)
- [`subMonths()`](#submonths)
- [`startOfDay()`](#startofday)
- [`endOfDay()`](#endofday)
- [`startOfMonth()`](#startofmonth)
- [`endOfMonth()`](#endofmonth)
- [`startOfYear()`](#startofyear)
- [`endOfYear()`](#endofyear)

### Formatting

- [`toAtomString()`](#toatomstring)
- [`toIso8601String()`](#toiso8601string)
- [`toRfc822String()`](#torfc822string)
- [`toW3cString()`](#tow3cstring)
- [`toDateString()`](#todatestring)
- [`toDateTimeString()`](#todatetimestring)
- [`isoFormat()`](#isoformat)

### Calculations

- [`isNowOrFuture()`](#isnoworfuture)
- [`secondsSinceMidnight()`](#secondssincemidnight)
- [`ago()`](#ago)
- [`elapsedTime()`](#elapsedtime)
- [`secondsFromIsoDuration()`](#secondsfromisoduration)

## Dynamic Properties

`DatetimeHelper` exposes additional read-only properties for commonly used date and time components.

| Property        | Type     | Description                                      |
|-----------------|----------|--------------------------------------------------|
| `$year`         | `int`    | Four-digit year                                  |
| `$month`        | `int`    | Month, from 1 to 12                              |
| `$day`          | `int`    | Day of the month                                 |
| `$hour`         | `int`    | Hour, from 0 to 23                               |
| `$minute`       | `int`    | Minute, from 0 to 59                             |
| `$second`       | `int`    | Second, from 0 to 59                             |
| `$micro`        | `int`    | Microseconds                                     |
| `$microsecond`  | `int`    | Alias for `$micro`                               |
| `$timestamp`    | `int`    | Unix timestamp in seconds                        |
| `$dayOfWeek`    | `int`    | Day of the week, from 0 (Sunday) to 6 (Saturday) |
| `$dayOfYear`    | `int`    | Day of the year, from 1 to 366                   |
| `$weekOfYear`   | `int`    | ISO week number, from 1 to 53                    |
| `$daysInMonth`  | `int`    | Number of days in the current month              |
| `$timezone`     | `string` | Name of the instance timezone                    |
| `$timezoneName` | `string` | Alias for `$timezone`                            |

These properties are read-only. Unknown dynamic property names return `null`.

### Example

```php
use Moudarir\Helpers\Datetime\DatetimeHelper;

$date = new DatetimeHelper('2024-02-29 12:30:00');

$year = $date->year;
$month = $date->month;
$days = $date->daysInMonth;
```

## `now()`

```php
public static function now(): static
```

Creates a new instance representing the current date and time in the configured default timezone.

The default timezone is `UTC`.

### Return Value

Returns a new instance of the called class.

### Example

```php
$date = DatetimeHelper::now();
```

---

## `timeZone()`

```php
public static function timeZone(): DateTimeZone
```

Returns the configured default timezone as a `DateTimeZone` object.

The timezone object is cached after its first creation.

### Return Value

Returns the default timezone.

### Exceptions

Throws `DateInvalidTimeZoneException` if the configured timezone is invalid.

### Example

```php
$timezone = DatetimeHelper::timeZone();

echo $timezone->getName();
```

Result:

```text
UTC
```

---

## `changeTimeZone()`

```php
public static function changeTimeZone(string $timezone): void
```

Changes the default timezone used when creating new instances and parsing dates without an explicitly supplied timezone.

The supplied timezone is validated before the configuration is changed. The timezone cache and formatter cache are cleared after a successful change.

Existing instances retain their own timezone.

### Parameters

| Parameter   | Type     | Description               |
|-------------|----------|---------------------------|
| `$timezone` | `string` | Valid timezone identifier |

### Exceptions

Throws `DateInvalidTimeZoneException` if the timezone identifier is invalid. In that case, the existing configuration remains unchanged.

### Example

```php
DatetimeHelper::changeTimeZone('Europe/Paris');

$date = DatetimeHelper::now();
```

The newly created instance uses `Europe/Paris`.

---

## `setLocale()`

```php
public static function setLocale(string $locale): void
```

Sets the locale used for date formatting and selects the language file used by `DatetimeHelper`.

The language file is selected using the language code extracted from the locale. Regional variants of the same language share the same language file.

If no matching language file exists, the French language file is used.

Changing the locale resets the loaded language configuration and the formatter caches.

### Parameters

| Parameter | Type     | Description                                             |
|-----------|----------|---------------------------------------------------------|
| `$locale` | `string` | Locale identifier, such as `fr_FR`, `en_US`, or `ar_MA` |

### Example

```php
DatetimeHelper::setLocale('ar_MA');

$date = new DatetimeHelper('2026-10-12 12:00:00');

echo $date->isoFormat('L');
echo $date->ago();
```

In this example, the Arabic language file is used for localized text, while `ar_MA` remains the locale used for date formatting.

## Language Files

Language files provide the localized text and ICU MessageFormat patterns used by `DatetimeHelper`.

The files are stored in `src/Datetime/Lang/`:

| File     | Language |
|----------|----------|
| `fr.php` | French   |
| `en.php` | English  |
| `ar.php` | Arabic   |

Each file returns an array containing the following keys:

| Key            | Description                                        |
|----------------|----------------------------------------------------|
| `isoFormatMap` | ICU date patterns for the supported format aliases |
| `unitsShort`   | Patterns for short elapsed duration units          |
| `unitsLong`    | Patterns for full elapsed duration units           |
| `zeroText`     | Patterns used for zero or negative durations       |
| `agoTexts`     | Text templates and unit patterns used by `ago()`   |

### Language Selection

The language file is selected from the language portion of the configured locale.

For example:

| Locale  | Language file                        |
|---------|--------------------------------------|
| `fr`    | `fr.php`                             |
| `fr_FR` | `fr.php`                             |
| `en_US` | `en.php`                             |
| `ar_MA` | `ar.php`                             |
| `br`    | `fr.php`, if `br.php` is unavailable |

The region portion of the locale remains available to ICU date formatting. The language file provides the corresponding text and predefined date format patterns.

Language files are loaded lazily and cached. Calling `setLocale()` invalidates the loaded language configuration and both formatter caches, so subsequent calls use the new locale and language file.

### ICU Requirements

The `intl` PHP extension is required for ICU-based date formatting and plural formatting.

Plural patterns select the appropriate grammatical form according to the configured locale. For example, Arabic uses plural categories including `zero`, `one`, `two`, `few`, `many`, and `other`.

The language files contain these patterns, so applications do not need to define their own pluralization rules.

---

## `parseFromFormat()`

```php
public static function parseFromFormat(int|string $time): ?static
```

Creates a date instance from a Unix timestamp or a date string.

Despite the method name, this method does not accept a separate date format string.

Integer arguments and strings containing a signed integer are interpreted as Unix timestamps in seconds. Other strings are passed to `getFromFormat()` for native date parsing.

### Parameters

| Parameter | Type          | Description                   |
|-----------|---------------|-------------------------------|
| `$time`   | `int\|string` | Unix timestamp or date string |

### Return Value

Returns a new instance of the called class, or `null` if parsing fails.

### Examples

Parse a Unix timestamp:

```php
$date = DatetimeHelper::parseFromFormat(0);
```

Result with the default UTC timezone:

```text
1970-01-01 00:00:00
```

Parse a date string:

```php
$date = DatetimeHelper::parseFromFormat(
    '2024-02-29 12:30:00'
);
```

---

## `getFromTimestamp()`

```php
public static function getFromTimestamp(int $timestamp): ?static
```

Creates a date instance from a Unix timestamp in seconds.

The resulting instance is converted to the configured default timezone.

### Parameters

| Parameter    | Type  | Description               |
|--------------|-------|---------------------------|
| `$timestamp` | `int` | Unix timestamp in seconds |

### Return Value

Returns a new instance of the called class, or `null` if an exception occurs.

### Example

```php
$date = DatetimeHelper::getFromTimestamp(0);
```

With the default UTC timezone, the result represents midnight on January 1, 1970.

---

## `getFromFormat()`

```php
public static function getFromFormat(string $time): ?static
```

Parses a date string using PHP's native date and time parser.

When the string does not specify a timezone, the configured default timezone is used. An explicitly supplied timezone in the string is respected by the native parser.

### Parameters

| Parameter | Type     | Description              |
|-----------|----------|--------------------------|
| `$time`   | `string` | Date or date-time string |

### Return Value

Returns a new instance of the called class, or `null` if parsing throws an exception.

### Example

```php
$date = DatetimeHelper::getFromFormat(
    '2024-02-29 12:30:00'
);
```

An explicit timezone can also be supplied:

```php
$date = DatetimeHelper::getFromFormat(
    '2024-02-29T12:30:00+02:00'
);
```

---

## `modify()`

```php
public function modify(string $modifier): static
```

Returns a new instance with the requested date or time modification applied.

The method uses the native `DateTimeImmutable::modify()` implementation and preserves the concrete class type.

If an exception occurs, the exception is reported through the protected `reportException()` hook and the original instance is returned.

### Parameters

| Parameter   | Type     | Description                          |
|-------------|----------|--------------------------------------|
| `$modifier` | `string` | Date or time modification expression |

### Return Value

Returns the modified instance, or the original instance if the operation fails.

### Example

```php
$date = new DatetimeHelper('2024-01-02 12:00:00');

$nextDay = $date->modify('+1 day');
```

The original instance remains unchanged.

---

## `addHours()`

```php
public function addHours(int $hours): static
```

Returns a new instance with the specified number of hours added.

### Parameters

| Parameter | Type  | Description            |
|-----------|-------|------------------------|
| `$hours`  | `int` | Number of hours to add |

### Example

```php
$date = DatetimeHelper::getFromFormat(
    '2024-01-02 12:00:00'
);

$next = $date?->addHours(2);
```

Result:

```text
2024-01-02 14:00:00
```

---

## `subHours()`

```php
public function subHours(int $hours): static
```

Returns a new instance with the specified number of hours subtracted.

### Parameters

| Parameter | Type  | Description                 |
|-----------|-------|-----------------------------|
| `$hours`  | `int` | Number of hours to subtract |

### Example

```php
$date = new DatetimeHelper('2024-01-02 12:00:00');

$previous = $date->subHours(2);
```

Result:

```text
2024-01-02 10:00:00
```

---

## `addDays()`

```php
public function addDays(int $days): static
```

Returns a new instance with the specified number of days added.

### Parameters

| Parameter | Type  | Description           |
|-----------|-------|-----------------------|
| `$days`   | `int` | Number of days to add |

### Example

```php
$date = new DatetimeHelper('2024-01-02 12:00:00');

$next = $date->addDays(2);
```

Result:

```text
2024-01-04 12:00:00
```

---

## `subDays()`

```php
public function subDays(int $days): static
```

Returns a new instance with the specified number of days subtracted.

### Parameters

| Parameter | Type  | Description                |
|-----------|-------|----------------------------|
| `$days`   | `int` | Number of days to subtract |

### Example

```php
$date = new DatetimeHelper('2024-01-02 12:00:00');

$previous = $date->subDays(2);
```

Result:

```text
2023-12-31 12:00:00
```

---

## `addMonths()`

```php
public function addMonths(int $months = 1): static
```

Returns a new instance with the specified number of months added.

### Parameters

| Parameter | Type  | Description             |
|-----------|-------|-------------------------|
| `$months` | `int` | Number of months to add |

### Example

```php
$date = new DatetimeHelper('2024-01-15 12:00:00');

$next = $date->addMonths(2);
```

Result:

```text
2024-03-15 12:00:00
```

---

## `subMonths()`

```php
public function subMonths(int $months = 1): static
```

Subtracts the specified number of months from the current date and returns a new instance.

### Parameters

| Parameter | Type  | Description                  |
|-----------|-------|------------------------------|
| `$months` | `int` | Number of months to subtract |

### Example

```php
$date = new DatetimeHelper('2024-03-15 12:00:00');

$previous = $date->subMonths(2);
```

Result:

```text
2024-01-15 12:00:00
```

Month modification follows PHP's native date modification rules. Overflow can occur when the target month does not contain the current
day number.

To calculate the previous calendar month reliably, call `startOfMonth()` before `subMonths()`.

Example:

```php
$date = new DatetimeHelper('2026-03-31 12:00:00');

$previousMonth = $date->startOfMonth()->subMonths();
```

Result:

```text
2026-02-01 00:00:00
```

---

## `startOfDay()`

```php
public function startOfDay(): static
```

Returns a new instance set to midnight on the current day.

### Example

```php
$date = new DatetimeHelper('2024-01-02 12:30:45');

$start = $date->startOfDay();
```

Result:

```text
2024-01-02 00:00:00
```

---

## `endOfDay()`

```php
public function endOfDay(): static
```

Returns a new instance set to the last microsecond of the current day.

### Example

```php
$date = new DatetimeHelper('2024-01-02 12:30:45');

$end = $date->endOfDay();
```

Result:

```text
2024-01-02 23:59:59.999999
```

---

## `startOfMonth()`

```php
public function startOfMonth(): static
```

Returns a new instance set to the first day of the month at midnight.

### Example

```php
$date = new DatetimeHelper('2024-02-15 12:30:45');

$start = $date->startOfMonth();
```

Result:

```text
2024-02-01 00:00:00
```

---

## `endOfMonth()`

```php
public function endOfMonth(): static
```

Returns a new instance set to the last microsecond of the month.

### Example

```php
$date = new DatetimeHelper('2024-02-15 12:30:45');

$end = $date->endOfMonth();
```

Result:

```text
2024-02-29 23:59:59.999999
```

---

## `startOfYear()`

```php
public function startOfYear(): static
```

Returns a new instance set to January 1 at midnight of the current year.

### Example

```php
$date = new DatetimeHelper('2024-02-29 12:30:45');

$start = $date->startOfYear();
```

Result:

```text
2024-01-01 00:00:00
```

---

## `endOfYear()`

```php
public function endOfYear(): static
```

Returns a new instance set to the last microsecond of the current year.

### Example

```php
$date = new DatetimeHelper('2024-02-29 12:30:45');

$end = $date->endOfYear();
```

Result:

```text
2024-12-31 23:59:59.999999
```

---

## `toAtomString()`

```php
public function toAtomString(): string
```

Formats the date using PHP's `DateTimeInterface::ATOM` format.

The result includes the date, time, and timezone offset.

### Example

```php
$date = new DatetimeHelper(
    '2024-01-02 03:04:05',
    new DateTimeZone('UTC')
);

$result = $date->toAtomString();
```

Result:

```text
2024-01-02T03:04:05+00:00
```

---

## `toIso8601String()`

```php
public function toIso8601String(): string
```

Returns the same representation as `toAtomString()`.

### Example

```php
$result = DatetimeHelper::now()->toIso8601String();
```

---

## `toRfc822String()`

```php
public function toRfc822String(): string
```

Formats the date using PHP's `DateTimeInterface::RFC822` format.

### Example

```php
$date = new DatetimeHelper(
    '2024-01-02 03:04:05',
    new DateTimeZone('UTC')
);

$result = $date->toRfc822String();
```

Result:

```text
Tue, 02 Jan 24 03:04:05 +0000
```

---

## `toW3cString()`

```php
public function toW3cString(): string
```

Formats the date using PHP's `DateTimeInterface::W3C` format.

### Example

```php
$date = new DatetimeHelper(
    '2024-01-02 03:04:05',
    new DateTimeZone('UTC')
);

$result = $date->toW3cString();
```

Result:

```text
2024-01-02T03:04:05+00:00
```

---

## `toDateString()`

```php
public function toDateString(): string
```

Returns the date in `Y-m-d` format.

### Example

```php
$result = DatetimeHelper::now()->toDateString();
```

Result:

```text
2024-01-02
```

---

## `toDateTimeString()`

```php
public function toDateTimeString(): string
```

Returns the date and time in `Y-m-d H:i:s` format, without a timezone suffix or fractional seconds.

### Example

```php
$date = new DatetimeHelper('2024-01-02 03:04:05');

$result = $date->toDateTimeString();
```

Result:

```text
2024-01-02 03:04:05
```

---

## `isoFormat()`

```php
public function isoFormat(string $pattern): string
```

Formats the date using an ICU date pattern through PHP's `IntlDateFormatter`.

The method supports predefined aliases as well as custom ICU patterns. It uses the configured locale and the timezone of the current instance.

### Supported Aliases

| Alias  | ICU pattern              | Description                        |
|--------|--------------------------|------------------------------------|
| `L`    | `dd/MM/yyyy`             | Numeric date                       |
| `LL`   | `d MMMM yyyy`            | Date with full month name          |
| `LLL`  | `d MMMM yyyy HH:mm`      | Date and time                      |
| `LLLL` | `EEEE d MMMM yyyy HH:mm` | Full weekday, date and time        |
| `l`    | `d/M/yyyy`               | Short numeric date                 |
| `ll`   | `d MMM yyyy`             | Date with abbreviated month        |
| `lll`  | `d MMM yyyy HH:mm`       | Short date and time                |
| `llll` | `EEE d MMM yyyy HH:mm`   | Abbreviated weekday, date and time |

### Parameters

| Parameter  | Type     | Description                    |
|------------|----------|--------------------------------|
| `$pattern` | `string` | Supported alias or ICU pattern |

### Return Value

Returns the formatted date string.

Returns an empty string if formatting fails. Exceptions are passed to the protected `reportException()` hook.

### Requirements

The `ext-intl` PHP extension is required for this method.

### Example

```php
$date = new DatetimeHelper(
    '2024-02-29 12:30:00',
    new DateTimeZone('Europe/Paris')
);

$result = $date->isoFormat('LLLL');
```

The output uses the configured locale and the instance's timezone.

Custom ICU patterns can be used directly:

```php
$result = $date->isoFormat('yyyy-MM-dd HH:mm:ss');
```

---

## `isNowOrFuture()`

```php
public function isNowOrFuture(): bool
```

Determines whether the instance's timestamp is greater than or equal to the current system timestamp.

### Return Value

Returns `true` if the instance represents the present or a future instant; otherwise, returns `false`.

### Example

```php
$date = new DatetimeHelper('2099-01-01 00:00:00');

$isFuture = $date->isNowOrFuture();
```

---

## `secondsSinceMidnight()`

```php
public function secondsSinceMidnight(): int
```

Calculates the number of elapsed seconds since midnight according to the instance's timezone.

Fractional seconds are not included.

### Return Value

Returns an integer between `0` and `86399`.

### Example

```php
$date = new DatetimeHelper(
    '2024-01-02 03:04:05',
    new DateTimeZone('UTC')
);

$seconds = $date->secondsSinceMidnight();
```

Result:

```text
11045
```

---

## `ago()`

```php
public function ago(
    ?DateTimeInterface $other = null,
    int $parts = 1,
    bool $absolute = false
): string
```

Returns a human-readable representation of the difference between the current instance and another date.

The method supports multiple units, including years, months, weeks, days, hours, minutes, and seconds.

If `$other` is omitted, the current date and time from `DatetimeHelper::now()` is used as the comparison date.

### Parameters

| Parameter   | Type                 | Default | Description                                    |
|-------------|----------------------|---------|------------------------------------------------|
| `$other`    | `?DateTimeInterface` | `null`  | Date used for comparison                       |
| `$parts`    | `int`                | `1`     | Maximum number of units displayed, from 1 to 7 |
| `$absolute` | `bool`               | `false` | Omits the past/future prefix when enabled      |

### Return Value

Returns a localized human-readable date difference.

When the difference is less than five seconds and `$absolute` is `false`, the method returns the configured `now` text.

With `$absolute = true`, the relative prefix is omitted. Identical dates return `0 secondes`.

If an exception occurs, the method reports it through `reportException()` and returns an empty string.

### Examples

Display a past date difference:

```php
$date = new DatetimeHelper('2024-01-02 10:00:00');

$other = new DateTimeImmutable(
    '2024-01-02 12:15:30',
    new DateTimeZone('UTC')
);

$result = $date->ago($other, 2);
```

Result:

```text
il y a 2 heures 15 minutes
```

Display an absolute difference without the relative prefix:

```php
$result = $date->ago($other, 2, true);
```

Result:

```text
2 heures 15 minutes
```

---

## `elapsedTime()`

```php
public static function elapsedTime(
    float|int $time,
    int $parts = 2,
    bool $short = true
): string
```

Converts a duration expressed in seconds into a human-readable representation.

The method supports days, hours, minutes, seconds, and milliseconds.

The `$parts` parameter controls the maximum number of non-zero units included in the result. Values below `1` are treated as `1`.

### Parameters

| Parameter | Type         | Default | Description                          |
|-----------|--------------|---------|--------------------------------------|
| `$time`   | `float\|int` | —       | Duration in seconds                  |
| `$parts`  | `int`        | `2`     | Maximum number of units displayed    |
| `$short`  | `bool`       | `true`  | Uses abbreviated labels when enabled |

### Return Value

Returns the formatted duration.

Zero and negative durations return the configured zero text.

### Examples

Use abbreviated labels:

```php
$result = DatetimeHelper::elapsedTime(65.5, 2, true);
```

Result:

```text
1 min 5 s
```

Use full labels:

```php
$result = DatetimeHelper::elapsedTime(65.5, 2, false);
```

Result:

```text
1 minute 5 secondes
```

---

## `secondsFromIsoDuration()`

```php
public static function secondsFromIsoDuration(
    string $isoDuration
): int
```

Converts an ISO 8601 duration into a total number of seconds.

Days, hours, minutes, and seconds have fixed conversions and are supported. Calendar-based years and months are not converted because their duration in seconds depends on the calendar reference.

### Parameters

| Parameter      | Type     | Description              |
|----------------|----------|--------------------------|
| `$isoDuration` | `string` | ISO 8601 duration string |

### Return Value

Returns the total number of seconds.

Returns `0` for an empty or invalid duration and for durations containing years or months.

### Examples

Convert hours, minutes, and seconds:

```php
$seconds = DatetimeHelper::secondsFromIsoDuration(
    'PT2H30M15S'
);
```

Result:

```text
9015
```

Convert days and hours:

```php
$seconds = DatetimeHelper::secondsFromIsoDuration('P1DT2H');
```

Result:

```text
93600
```

Calendar-based durations are not converted:

```php
$seconds = DatetimeHelper::secondsFromIsoDuration('P1M');
```

Result:

```text
0
```

Note that `P1M` represents one month, while `PT1M` represents one minute.

---

## Serialization

`DatetimeHelper` supports PHP's native serialization mechanisms.

Serialization preserves the date, fractional seconds, and timezone. During unserialization, invalid data is reported through `reportException()` and the instance falls back to the current time in UTC.

### Example

```php
$date = new DatetimeHelper(
    '2024-02-29 12:30:00.123456',
    new DateTimeZone('Europe/Paris')
);

$restored = unserialize(serialize($date));
```

The restored instance preserves the original date and timezone.

---

## Requirements

- PHP 8.4 or higher.
- `ext-intl` for locale-aware formatting through `isoFormat()`.

The default timezone is `UTC`, and the default locale is `fr_FR`.

## Error Reporting

`DatetimeHelper` provides a protected `reportException()` hook.

Its default implementation does nothing. Subclasses can override it to integrate their own exception reporting mechanism.

Some methods intentionally handle exceptions and return fallback values instead of propagating those exceptions. See the individual method descriptions for their failure behavior.
