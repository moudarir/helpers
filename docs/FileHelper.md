# FileHelper

`FileHelper` provides lightweight helpers for common file operations, including filename generation, path information, file metadata, file writing, and reading or saving file contents.

## Available Methods

* [`newFilename()`](#newfilename)
* [`pathInfo()`](#pathinfo)
* [`info()`](#info)
* [`write()`](#write)
* [`getContent()`](#getcontent)
* [`saveContent()`](#savecontent)

### `newFilename()`

Generates an available filename based on a given file path.

When the target file already exists, the method either generates a random filename or adds an incremental numeric suffix.

#### Signature

```php
public static function newFilename(
    string $filepath,
    int $length = 40,
    bool $encrypt = true
): array
```

#### Parameters

* `$filepath` — Initial file path.
* `$length` — Length of the generated random filename when `$encrypt` is `true`.
* `$encrypt` — Generates a random filename when `true`, or uses incremental
  numeric suffixes when `false`.

#### Return Value

Returns an array containing:

* `file_name` — Final filename including its extension.
* `raw_name` — Final filename without its extension.
* `file_ext` — File extension including the leading `.` when present.
* `file_path` — Directory containing the final filename.
* `full_path` — Complete path of the available filename.

#### Examples

```php
$info = FileHelper::newFilename('/var/www/files/document.pdf');
```

When the file already exists, a random filename is generated:

```php
$info = FileHelper::newFilename('/var/www/files/document.pdf');

// Example:
// [
//     'file_name' => '8Ks4nP2x...pdf',
//     'raw_name' => '8Ks4nP2x...',
//     'file_ext' => '.pdf',
//     'file_path' => '/var/www/files',
//     'full_path' => '/var/www/files/8Ks4nP2x...pdf',
// ]
```

To use incremental suffixes instead:

```php
$info = FileHelper::newFilename('/var/www/files/document.pdf', encrypt: false);

// Example:
// document-2.pdf
```

---

### `pathInfo()`

Returns information extracted from a file path.

The method ensures that `filename` and `extension` are always present in the returned array, even when they are not provided by PHP's `pathinfo()`.

#### Signature

```php
public static function pathInfo(
    string $filepath,
    ?string $key = null
): array|string|null
```

#### Parameters

* `$filepath` — File path to inspect.
* `$key` — Optional information key to return.

#### Return Value

Returns the complete path information as an array when `$key` is `null`.

When `$key` is provided, returns its value or `null` when the key does not exist.

The returned array contains:

* `dirname`
* `basename`
* `filename`
* `extension`

#### Examples

```php
$info = FileHelper::pathInfo('/var/www/files/document.pdf');

// [
//     'dirname' => '/var/www/files',
//     'basename' => 'document.pdf',
//     'filename' => 'document',
//     'extension' => 'pdf',
// ]
```

To retrieve a specific value:

```php
$filename = FileHelper::pathInfo('/var/www/files/document.pdf', 'filename');

// document
```

---

### `info()`

Returns information about an existing file.

#### Signature

```php
public static function info(
    string $filepath,
    bool $optionals = false
): array
```

#### Parameters

* `$filepath` — File path to inspect.
* `$optionals` — Includes additional filesystem information when `true`.

#### Return Value

Returns an empty array when the file does not exist.

For an existing file, the returned array contains:

* `name` — Filename.
* `server_path` — Complete file path.
* `size` — File size in bytes.
* `date` — Last modification timestamp.

When `$optionals` is `true`, the following values are also included:

* `readable` — Whether the file is readable.
* `executable` — Whether the file is executable.
* `fileperms` — File permissions.

#### Examples

```php
$info = FileHelper::info('/var/www/files/document.pdf');
```

Including optional filesystem information:

```php
$info = FileHelper::info('/var/www/files/document.pdf', true);
```

---

### `write()`

Writes data to a file while using an exclusive file lock.

#### Signature

```php
public static function write(
    string $filepath,
    string $data,
    string $mode = 'wb'
): bool
```

#### Parameters

* `$filepath` — Destination file path.
* `$data` — Data to write.
* `$mode` — File opening mode passed to `fopen()`.

#### Return Value

Returns `true` when the write operation succeeds, otherwise `false`.

The method handles partial writes and releases the file lock before closing the file.

#### Examples

```php
FileHelper::write('/var/www/files/document.txt', 'Hello World');
```

Appending data:

```php
FileHelper::write('/var/www/files/document.txt', 'Hello World', 'ab');
```

---

### `getContent()`

Reads the contents of a file or stream resource accessible through `file_get_contents()`.

#### Signature

```php
public static function getContent(
    string $filepath,
    bool $isUrl = false
): string
```

#### Parameters

* `$filepath` — File path or URL to read.
* `$isUrl` — Indicates that the value should be handled as a URL.

#### Return Value

Returns the file contents as a string.

#### Exceptions

Throws `ErrorException` when the file does not exist or its contents cannot be read.

#### Examples

```php
$content = FileHelper::getContent('/var/www/files/document.txt');
```

Reading a URL:

```php
$content = FileHelper::getContent('https://example.com/data.txt', true);
```

---

### `saveContent()`

Saves data to a file using `file_put_contents()`.

#### Signature

```php
public static function saveContent(
    string $filepath,
    string $data
): true
```

#### Parameters

* `$filepath` — Destination file path.
* `$data` — Data to save.

#### Return Value

Returns `true` when the content is successfully saved.

#### Exceptions

Throws `ErrorException` when the content cannot be saved.

#### Examples

```php
FileHelper::saveContent('/var/www/files/document.txt', 'Hello World');
```
