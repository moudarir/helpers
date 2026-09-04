# IsHelper

`IsHelper` provides lightweight validation helpers for common string, network, and data-format checks.

## Available Methods

* [`validUrl()`](#validurl)
* [`validIP()`](#validip)
* [`validEmail()`](#validemail)
* [`validMac()`](#validmac)
* [`validBase64()`](#validbase64)
* [`naturalNoZero()`](#naturalnozero)
* [`alpha()`](#alpha)
* [`alphaNumeric()`](#alphanumeric)
* [`alphaNumericSpaces()`](#alphanumericspaces)
* [`alphaDash()`](#alphadash)
* [`alnumDash()`](#alnumdash)
* [`alnumUnderscore()`](#alnumunderscore)
* [`numeric()`](#numeric)
* [`integer()`](#integer)
* [`decimal()`](#decimal)
* [`hex()`](#hex)
* [`containsArabic()`](#containsarabic)
* [`onlyArabic()`](#onlyarabic)

### `validUrl()`

Validates a URL and optionally restricts the allowed protocols.

#### Signature

```php
public static function validUrl(string $value, array $protocols = []): bool
```

#### Parameters

* `$value` — URL to validate.
* `$protocols` — Optional list of `EnumProtocol` values. Invalid elements are ignored. When no valid protocol is provided, all `EnumProtocol` cases are allowed.

#### Return Value

Returns `true` when the value is a valid URL using an allowed protocol, otherwise `false`.

#### Examples

```php
use Moudarir\Helpers\Enums\EnumProtocol;
use Moudarir\Helpers\IsHelper;

IsHelper::validUrl('https://example.com');
// true

IsHelper::validUrl('https://example.com', [EnumProtocol::HTTP, EnumProtocol::HTTPS]);
// true

IsHelper::validUrl('ftp://example.com', [EnumProtocol::HTTP, EnumProtocol::HTTPS]);
// false
```

Invalid protocol values are ignored:

```php
IsHelper::validUrl('https://example.com', ['invalid', EnumProtocol::HTTPS]);
// true
```

When no valid `EnumProtocol` is provided, all supported protocols are used:

```php
IsHelper::validUrl('https://example.com', ['invalid']);
// true
```

---

### `validIP()`

Validates an IPv4 or IPv6 address, with optional restriction to one IP version.

#### Signature

```php
public static function validIP(?string $ip = null, string $which = ''): bool
```

#### Parameters

* `$ip` — IP address to validate.
* `$which` — Optional IP version: `ipv4` or `ipv6`. Any other value validates both versions.

#### Return Value

Returns `true` when the IP address is valid according to the selected version, otherwise `false`.

#### Examples

```php
IsHelper::validIP('192.168.1.1');
// true

IsHelper::validIP('2001:db8::1');
// true

IsHelper::validIP('192.168.1.1', 'ipv4');
// true

IsHelper::validIP('2001:db8::1', 'ipv6');
// true

IsHelper::validIP('2001:db8::1', 'ipv4');
// false
```

---

### `validEmail()`

Validates an email address.

When the `intl` extension is available, internationalized domains are converted to ASCII before validation.

#### Signature

```php
public static function validEmail(string $str): bool
```

#### Parameters

* `$str` — Email address to validate.

#### Return Value

Returns `true` when the email address is valid, otherwise `false`.

#### Examples

```php
IsHelper::validEmail('user@example.com');
// true

IsHelper::validEmail('user+tag@example.com');
// true

IsHelper::validEmail('not-an-email');
// false
```

---

### `validMac()`

Validates a MAC address.

#### Signature

```php
public static function validMac(string $mac): bool
```

#### Parameters

* `$mac` — MAC address to validate.

#### Return Value

Returns `true` when the MAC address is valid, otherwise `false`.

#### Examples

```php
IsHelper::validMac('00:11:22:33:44:55');
// true

IsHelper::validMac('00-11-22-33-44-55');
// true

IsHelper::validMac('not-a-mac');
// false
```

---

### `validBase64()`

Checks whether a string is a valid Base64 representation.

#### Signature

```php
public static function validBase64(string $str): bool
```

#### Parameters

* `$str` — Base64-encoded string to validate.

#### Return Value

Returns `true` when the string can be decoded and re-encoded to the same representation, otherwise `false`.

#### Examples

```php
IsHelper::validBase64('SGVsbG8gV29ybGQ=');
// true

IsHelper::validBase64('not-base64');
// false
```

---

### `natural()`

Checks whether a string contains only decimal digits.

#### Signature

```php
public static function natural(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only digits, otherwise `false`.

#### Examples

```php
IsHelper::natural('123');
// true

IsHelper::natural('0');
// true

IsHelper::natural('00123');
// true

IsHelper::natural('-123');
// false
```

---

### `naturalNoZero()`

Checks whether a string represents a natural number other than zero.

#### Signature

```php
public static function naturalNoZero(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only digits and represents a non-zero value, otherwise `false`.

#### Examples

```php
IsHelper::naturalNoZero('123');
// true

IsHelper::naturalNoZero('0');
// false

IsHelper::naturalNoZero('000');
// false

IsHelper::naturalNoZero('-123');
// false
```

---

### `alpha()`

Checks whether a string contains only alphabetic characters.

#### Signature

```php
public static function alpha(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only alphabetic characters, otherwise `false`.

#### Examples

```php
IsHelper::alpha('Hello');
// true

IsHelper::alpha('Hello123');
// false

IsHelper::alpha('Hello World');
// false
```

---

### `alphaNumeric()`

Checks whether a string contains only alphabetic characters and digits.

#### Signature

```php
public static function alphaNumeric(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only alphabetic characters and digits, otherwise `false`.

#### Examples

```php
IsHelper::alphaNumeric('Hello123');
// true

IsHelper::alphaNumeric('Hello 123');
// false

IsHelper::alphaNumeric('Hello-123');
// false
```

---

### `alphaNumericSpaces()`

Checks whether a string contains only alphabetic characters, digits, and spaces.

#### Signature

```php
public static function alphaNumericSpaces(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only alphabetic characters, digits, and spaces, otherwise `false`.

#### Examples

```php
IsHelper::alphaNumericSpaces('Hello World 123');
// true

IsHelper::alphaNumericSpaces('Hello-123');
// false
```

---

### `alphaDash()`

Checks whether a string contains only alphabetic characters, digits, underscores, and hyphens.

#### Signature

```php
public static function alphaDash(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only alphabetic characters, digits, underscores, and hyphens, otherwise `false`.

#### Examples

```php
IsHelper::alphaDash('hello-world');
// true

IsHelper::alphaDash('hello_world_123');
// true

IsHelper::alphaDash('hello world');
// false
```

---

### `alnumDash()`

Checks whether a string contains only alphabetic characters, digits, and hyphens.

#### Signature

```php
public static function alnumDash(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only alphabetic characters, digits, and hyphens, otherwise `false`.

#### Examples

```php
IsHelper::alnumDash('hello-world-123');
// true

IsHelper::alnumDash('hello_world');
// false
```

---

### `alnumUnderscore()`

Checks whether a string contains only alphabetic characters, digits, and underscores.

#### Signature

```php
public static function alnumUnderscore(string $str): bool
```

#### Parameters

* `$str` — String to validate.

#### Return Value

Returns `true` when the string contains only alphabetic characters, digits, and underscores, otherwise `false`.

#### Examples

```php
IsHelper::alnumUnderscore('hello_world_123');
// true

IsHelper::alnumUnderscore('hello-world');
// false
```

---

### `numeric()`

Checks whether a string represents an integer or decimal number.

A leading `+` or `-` is allowed. The decimal separator is `.`.

#### Signature

```php
public static function numeric(string $str): bool
```

#### Parameters

* `$str` — Numeric string to validate.

#### Return Value

Returns `true` when the string represents a supported numeric format, otherwise `false`.

#### Examples

```php
IsHelper::numeric('123');
// true

IsHelper::numeric('-123.45');
// true

IsHelper::numeric('.45');
// true

IsHelper::numeric('1e10');
// false
```

---

### `integer()`

Checks whether a string represents an integer.

A leading `+` or `-` is allowed.

#### Signature

```php
public static function integer(string $str): bool
```

#### Parameters

* `$str` — Integer string to validate.

#### Return Value

Returns `true` when the string represents an integer, otherwise `false`.

#### Examples

```php
IsHelper::integer('123');
// true

IsHelper::integer('-123');
// true

IsHelper::integer('+123');
// true

IsHelper::integer('12.5');
// false
```

---

### `decimal()`

Checks whether a string represents a decimal number with digits on both sides of the decimal separator.

A leading `+` or `-` is allowed.

#### Signature

```php
public static function decimal(string $str): bool
```

#### Parameters

* `$str` — Decimal string to validate.

#### Return Value

Returns `true` when the string represents a decimal number, otherwise `false`.

#### Examples

```php
IsHelper::decimal('123.45');
// true

IsHelper::decimal('-123.45');
// true

IsHelper::decimal('.45');
// false

IsHelper::decimal('123');
// false
```

---

### `hex()`

Checks whether a string contains a hexadecimal value.

The optional `0x` or `0X` prefix is accepted.

#### Signature

```php
public static function hex(string $content): bool
```

#### Parameters

* `$content` — Hexadecimal value to validate.

#### Return Value

Returns `true` when the value contains only hexadecimal digits, otherwise `false`.

#### Examples

```php
IsHelper::hex('ABCDEF');
// true

IsHelper::hex('0xABCDEF');
// true

IsHelper::hex('0X123456');
// true

IsHelper::hex('123Z');
// false
```

---

### `containsArabic()`

Checks whether a string contains at least one character belonging to the Arabic Unicode script.

The string may contain characters from other scripts as well.

#### Signature

```php
public static function containsArabic(string $string): bool
```

#### Parameters

* `$string` — String to inspect.

#### Return Value

Returns `true` when at least one Arabic character is present, otherwise `false`.

#### Examples

```php
IsHelper::containsArabic('مرحبا');
// true

IsHelper::containsArabic('Hello مرحبا');
// true

IsHelper::containsArabic('Hello');
// false

IsHelper::containsArabic('');
// false
```

---

### `onlyArabic()`

Checks whether a string contains no letters from scripts other than Arabic.

Arabic characters are accepted together with numbers, punctuation, whitespace, and symbols.

An empty string is considered valid.

#### Signature

```php
public static function onlyArabic(string $string): bool
```

#### Parameters

* `$string` — String to validate.

#### Return Value

Returns `true` when the string contains no non-Arabic letters, otherwise `false`.

#### Examples

```php
IsHelper::onlyArabic('مرحبا بالعالم');
// true

IsHelper::onlyArabic('مرحبا 123');
// true

IsHelper::onlyArabic('مرحبا، كيف حالك؟');
// true

IsHelper::onlyArabic('مرحبا ❤️');
// true

IsHelper::onlyArabic('');
// true

IsHelper::onlyArabic('مرحبا Hello');
// false
```
