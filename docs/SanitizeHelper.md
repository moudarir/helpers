# SanitizeHelper

`SanitizeHelper` provides utilities for sanitizing and transforming text into URL-friendly formats, generating slugs, and removing accents from strings.

## Available Methods

* [`slugify()`](#slugify)
* [`urlTitle()`](#urltitle)
* [`removeAccents()`](#removeaccents)

---

## `slugify()`

```php
public static function slugify(
    string $title,
    array $additionalReplacements = []
): string
```

Converts a string into a lowercase URL-friendly slug.

The method removes HTML tags and accents, converts or removes specific characters, replaces spaces and separators with hyphens, and removes consecutive or leading/trailing hyphens.

Additional character replacements can be supplied to override the default replacements or add new ones.

### Parameters

| Parameter                 | Type     | Default | Description                                     |
| ------------------------- | -------- | ------- | ----------------------------------------------- |
| `$title`                  | `string` | —       | Text to convert into a slug                     |
| `$additionalReplacements` | `array`  | `[]`    | Additional or overriding character replacements |

### Default Replacements

The method applies the following replacements by default:

| Character | Replacement |
| --------- | ----------- |
| `%`       | `-pourcent` |
| `+ de`    | `plus-de`   |
| `+`       | `-plus`     |
| `₂`       | `2`         |
| `²`       | `2`         |
| `³`       | `3`         |
| `&`       | `et`        |

Custom replacements override values defined by default.

### Examples

Basic slug generation:

```php
$slug = SanitizeHelper::slugify('Créer un nouvel article');
```

Result:

```text
creer-un-nouvel-article
```

Overriding a default replacement:

```php
$slug = SanitizeHelper::slugify(
    '100% Rock & Roll',
    [
        '%' => '-percent',
        '&' => 'and',
    ]
);
```

Result:

```text
100-percent-rock-and-roll
```

Adding a new replacement:

```php
$slug = SanitizeHelper::slugify(
    'Hello @ World',
    ['@' => '-at']
);
```

Result:

```text
hello-at-world
```

Encoded octets such as `%20` are preserved during the transformation:

```php
$slug = SanitizeHelper::slugify('file%20name');
```

Result:

```text
file%20name
```

---

## `urlTitle()`

```php
public static function urlTitle(string $title): string
```

Converts a title into a simplified URL-friendly representation.

HTML tags and unsupported characters are removed, whitespace is converted to hyphens, and consecutive hyphens are collapsed.

Unlike `slugify()`, this method preserves the original character case and does not remove accents.

### Parameters

| Parameter | Type     | Description        |
| --------- | -------- | ------------------ |
| `$title`  | `string` | Title to transform |

### Return Value

Returns the transformed title.

Returns an empty string when the supplied title is empty or contains only whitespace.

### Example

```php
$title = SanitizeHelper::urlTitle('  Hello World  ');
```

Result:

```text
Hello-World
```

HTML tags are removed:

```php
$title = SanitizeHelper::urlTitle('<strong>Hello</strong> <em>World</em>');
```

Result:

```text
Hello-World
```

---

## `removeAccents()`

```php
public static function removeAccents(string $text): string
```

Removes or transliterates accented and special characters into their corresponding ASCII representations.

The method first normalizes Unicode text when the `intl` extension is available through `normalizer_normalize()`, then applies a predefined transliteration table.

### Parameters

| Parameter | Type     | Description                       |
| --------- | -------- | --------------------------------- |
| `$text`   | `string` | Text from which to remove accents |

### Return Value

Returns the normalized text.

ASCII-only strings are returned unchanged.

### Examples

```php
$text = SanitizeHelper::removeAccents('Café Crème Français');
```

Result:

```text
Cafe Creme Francais
```

Other characters are transliterated when supported:

```php
$text = SanitizeHelper::removeAccents('Æther Œuvre ß');
```

Result:

```text
AEther OEuvre ss
```

Some characters are intentionally removed or converted according to the helper's predefined transliteration rules.

## Requirements

`SanitizeHelper` uses `mbstring` and `intl` functionality.

* `ext-mbstring`
* `ext-intl`
