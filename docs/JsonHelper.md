# JsonHelper

`JsonHelper` provides lightweight helpers for encoding, decoding, and working with JSON data, including JSON Lines and JSON-LD formatted output.

## Available Methods

* [`decode()`](#decode)
* [`jsonify()`](#jsonify)
* [`encodeForLdFormat()`](#encodeforldformat)
* [`encodeAsJsonL()`](#encodeasjsonl)
* [`decodeFromJsonL()`](#decodefromjsonl)

---

## `decode()`

```php
public static function decode(string $string, bool $assoc = true): mixed
```

Decodes a JSON string after validating its syntax.

The `$assoc` parameter controls whether JSON objects are returned as associative arrays or as `stdClass` objects.

### Parameters

| Parameter | Type     | Default | Description                                |
| --------- | -------- | ------- | ------------------------------------------ |
| `$string` | `string` | —       | JSON string to decode                      |
| `$assoc`  | `bool`   | `true`  | Returns JSON objects as arrays when `true` |

### Return Value

Returns the decoded JSON value.

Depending on the input, the result can be:

* an associative array;
* an object;
* a string;
* an integer;
* a float;
* a boolean;
* `null`.

Returns `null` when the JSON string is invalid or represents the JSON `null` value.

### Example

```php
$data = JsonHelper::decode('{"name":"John","age":30}');
```

Result:

```php
[
    'name' => 'John',
    'age' => 30,
]
```

Using objects instead of associative arrays:

```php
$data = JsonHelper::decode('{"name":"John","age":30}', false);
```

---

## `jsonify()`

```php
public static function jsonify(
    array $data,
    bool $withHeader = true
): string
```

Encodes an array as JSON using the `JSON_HEX_TAG`, `JSON_HEX_APOS`, `JSON_HEX_AMP`, and `JSON_HEX_QUOT` options.

By default, the method also sends the following HTTP response header:

```text
Content-Type: application/json; charset=utf-8
```

The header can be disabled with `$withHeader = false`.

### Parameters

| Parameter     | Type    | Default | Description                        |
| ------------- | ------- | ------- | ---------------------------------- |
| `$data`       | `array` | —       | Data to encode                     |
| `$withHeader` | `bool`  | `true`  | Sends the JSON content type header |

### Return Value

Returns the encoded JSON string.

Returns an empty string when JSON encoding fails.

### Example

```php
$data = [
    'success' => true,
    'message' => 'Operation completed',
];

$json = JsonHelper::jsonify($data);
```

To encode the data without sending an HTTP header:

```php
$json = JsonHelper::jsonify($data, false);
```

---

## `encodeForLdFormat()`

```php
public static function encodeForLdFormat(array $schema): string
```

Encodes a schema as pretty-printed JSON and wraps it in a `application/ld+json` `<script>` element.

The JSON is encoded with `JSON_PRETTY_PRINT` and `JSON_UNESCAPED_SLASHES`.

### Parameters

| Parameter | Type    | Description              |
| --------- | ------- | ------------------------ |
| `$schema` | `array` | JSON-LD schema to encode |

### Return Value

Returns a complete HTML `<script>` element containing the encoded JSON.

Returns an empty string when JSON encoding fails.

### Example

```php
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'name' => 'Example article',
];

$html = JsonHelper::encodeForLdFormat($schema);
```

Result:

```html
<script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "Article",
    "name": "Example article"
}
</script>
```

---

## `encodeAsJsonL()`

```php
public static function encodeAsJsonL(array $data): string
```

Encodes a list of associative arrays using the [JSON Lines](https://jsonlines.org/) format.

Each array is encoded as one JSON object, and each object is separated from the next one by a newline.

### Parameters

| Parameter | Type                         | Description            |
| --------- | ---------------------------- | ---------------------- |
| `$data`   | `list<array<string, mixed>>` | JSON objects to encode |

### Return Value

Returns the JSON Lines representation of the supplied data.

An empty input array produces an empty string.

### Exceptions

Throws `JsonException` when one of the supplied values cannot be encoded as valid JSON.

### Example

```php
$data = [
    [
        'id' => '1',
        'company_name' => 'Stark Industries',
        'num_employees' => 5215,
        'country' => 'USA',
    ],
    [
        'id' => '2',
        'company_name' => 'Orbit Inc.',
        'num_employees' => 256,
        'country' => 'UK',
    ],
];

$jsonl = JsonHelper::encodeAsJsonL($data);
```

Result:

```json lines
{"id":"1","company_name":"Stark Industries","num_employees":5215,"country":"USA"}
{"id":"2","company_name":"Orbit Inc.","num_employees":256,"country":"UK"}
```

---

## `decodeFromJsonL()`

```php
public static function decodeFromJsonL(string $response): array
```

Decodes a JSON Lines string into a list of associative arrays.

Each non-empty line must contain one valid JSON object. Empty lines are ignored.

The method accepts different line ending conventions, including `LF`, `CRLF`, and `CR`.

### Parameters

| Parameter   | Type     | Description                  |
| ----------- | -------- | ---------------------------- |
| `$response` | `string` | JSON Lines content to decode |

### Return Value

Returns a sequential array containing the decoded JSON objects.

An empty string or content containing only empty lines returns an empty array.

### Exceptions

Throws `JsonException` when a non-empty line does not contain valid JSON.

### Example

```php
$response = <<<'JSON'
{"id":"1","company_name":"Stark Industries","num_employees":5215}
{"id":"2","company_name":"Orbit Inc.","num_employees":256}
JSON;

$data = JsonHelper::decodeFromJsonL($response);
```

Result:

```php
[
    [
        'id' => '1',
        'company_name' => 'Stark Industries',
        'num_employees' => 5215,
    ],
    [
        'id' => '2',
        'company_name' => 'Orbit Inc.',
        'num_employees' => 256,
    ],
]
```
