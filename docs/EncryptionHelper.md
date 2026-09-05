# EncryptionHelper

`EncryptionHelper` provides helpers for generating cryptographically secure random data, generating tokens, and encrypting and decrypting data using authenticated symmetric encryption.

## Available Methods

* [`binaryBytes()`](#binarybytes)
* [`generateToken()`](#generatetoken)
* [`encrypt()`](#encrypt)
* [`decrypt()`](#decrypt)

### `binaryBytes()`

Generates cryptographically secure random bytes.

#### Signature

```php
public static function binaryBytes(int $length): ?string
```

#### Parameters

* `$length` — Number of bytes to generate.

#### Return Value

Returns a binary string containing the requested number of random bytes.

Returns `null` when :

* `$length` is less than or equal to `0`;
* the random bytes cannot be generated.

#### Examples

```php
use Moudarir\Helpers\EncryptionHelper;

$bytes = EncryptionHelper::binaryBytes(32);
```

---

### `generateToken()`

Generates a cryptographically secure random token.

#### Signature

```php
public static function generateToken(
    int $length = 9,
    string $type = 'alnum',
    int $parts = 1
): string
```

#### Parameters

* `$length` — Number of characters generated for each part.
* `$type` — Character set used to generate the token.
* `$parts` — Number of token parts to generate.

Supported types are:

| Type      | Characters                    |
| --------- | ----------------------------- |
| `alpha`   | Letters                       |
| `alnum`   | Letters and numbers           |
| `upper`   | Uppercase letters and numbers |
| `numeric` | Numbers (`0-9`)               |
| `nozero`  | Numbers (`1-9`)               |

Any other value uses the default character set containing letters, numbers, and selected special characters.

When `$length` or `$parts` is less than or equal to `0`, the method returns an empty string.

When multiple parts are generated, they are separated by `-`.

#### Return Value

Returns a random token.

#### Exceptions

Throws `Random\RandomException` when secure random values cannot be generated.

#### Examples

```php
EncryptionHelper::generateToken();
// Example: "4fY8n2QaP"
```

```php
EncryptionHelper::generateToken(12, 'numeric');
// Example: "583920174628"
```

```php
EncryptionHelper::generateToken(8, 'alnum', 3);
// Example: "k7P2mQa9-8Fn3xR1-4Gt6Wz2M"
```

---

### `encrypt()`

Encrypts data using authenticated symmetric encryption provided by Sodium.

The supplied key is hashed with SHA-256 and the resulting 32-byte binary value is used as the encryption key.

A randomly generated nonce is stored together with the ciphertext. The complete binary payload is Base64 encoded before being returned.

#### Signature

```php
public static function encrypt(string $data, string $key): string
```

#### Parameters

* `$data` — Data to encrypt.
* `$key` — Encryption key.

#### Return Value

Returns the encrypted payload encoded as a Base64 string.

The returned value contains the nonce followed by the ciphertext.

#### Exceptions

Throws `Random\RandomException` when a secure nonce cannot be generated.

Throws `SodiumException` when Sodium cannot perform the encryption.

#### Examples

```php
$encrypted = EncryptionHelper::encrypt('Hello World', 'my-secret-key');
```

The returned value can be passed directly to `decrypt()`:

```php
$encrypted = EncryptionHelper::encrypt('Hello World', 'my-secret-key');

$decrypted = EncryptionHelper::decrypt($encrypted, 'my-secret-key');

// "Hello World"
```

The same input encrypted multiple times produces different encrypted values because a new random nonce is generated for each encryption.

---

### `decrypt()`

Decrypts data previously encrypted with `encrypt()`.

The supplied key is hashed with SHA-256 in the same way as during encryption.

#### Signature

```php
public static function decrypt(string $data, string $key): false|string
```

#### Parameters

* `$data` — Base64-encoded encrypted payload returned by `encrypt()`.
* `$key` — Decryption key.

#### Return Value

Returns the original plaintext when decryption succeeds.

Returns `false` when:

* the supplied data is not valid Base64;
* the decoded data does not contain a complete nonce;
* authentication fails;
* the data was modified;
* the wrong key is provided.

#### Exceptions

Throws `SodiumException` when Sodium cannot perform the decryption.

#### Examples

```php
$encrypted = EncryptionHelper::encrypt('Hello World', 'my-secret-key');

$decrypted = EncryptionHelper::decrypt($encrypted, 'my-secret-key');

// "Hello World"
```

Using an incorrect key:

```php
$encrypted = EncryptionHelper::encrypt('Hello World', 'my-secret-key');

$result = EncryptionHelper::decrypt($encrypted, 'wrong-key');

// false
```

A modified or invalid encrypted payload also results in `false`.

## Encryption Format

`encrypt()` stores the generated nonce together with the ciphertext:

```text
Base64(nonce + ciphertext)
```

The nonce is required by `decrypt()` to recover the original plaintext.

The encryption key supplied by the caller is not used directly. It is transformed using:

```php
hash('sha256', $key, true)
```

This produces the 32-byte key required by Sodium's `secretbox` API.
