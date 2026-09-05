# DirectoryHelper

`DirectoryHelper` provides helpers for listing, mapping, creating, inspecting and deleting directories and their contents.

## Available Methods

* [`recursively()`](#recursively)
* [`map()`](#map)
* [`create()`](#create)
* [`getFilesInfo()`](#getfilesinfo)
* [`deleteFiles()`](#deletefiles)

### `recursively()`

Returns all directories contained recursively within a directory.

The source directory itself is not included in the result.

#### Signature

```php
public static function recursively(string $directory): array
```

#### Parameters

* `$directory` — Directory to inspect.

#### Return Value

Returns an array containing the paths of all descendant directories.

Returns an empty array when the directory cannot be read.

#### Examples

```php
$directories = DirectoryHelper::recursively('/var/www/files');
```

---

### `map()`

Reads a directory and builds an array representation of its contents.

Subdirectories can be mapped recursively according to the configured depth.

#### Signature

```php
public static function map(string $directory, array $options = []): array
```

#### Parameters

* `$directory` — Directory to inspect.
* `$options` — Optional mapping options:
    * `depth` — Maximum recursion depth. `0` means unlimited recursion.
    * `hidden` — Includes hidden entries when `true`.
    * `filepath` — Returns complete paths instead of entry names when `true`.

#### Return Value

Returns an array representing the directory contents.

Directories are represented by array keys ending with the directory separator, while files are represented as array values.

#### Examples

```php
$files = DirectoryHelper::map('/var/www/files');
```

Limit recursion depth:

```php
$files = DirectoryHelper::map('/var/www/files', ['depth' => 2]);
```

Include hidden entries:

```php
$files = DirectoryHelper::map('/var/www/files', ['hidden' => true]);
```

Return full file paths:

```php
$files = DirectoryHelper::map('/var/www/files', ['filepath' => true]);
```

---

### `create()`

Creates a directory and all missing parent directories.

Existing directories are accepted and return `true`.

The method also supports paths using registered PHP stream wrappers.

#### Signature

```php
public static function create(string $directory): bool
```

#### Parameters

* `$directory` — Directory path to create.

#### Return Value

Returns `true` when the directory exists or is successfully created.
Returns `false` when the path cannot be created or already exists as a file.

#### Examples

```php
DirectoryHelper::create('/var/www/files/images');
```

Nested directories are created automatically:

```php
DirectoryHelper::create('/var/www/files/images/thumbnails');
```

---

### `getFilesInfo()`

Returns information about files contained in a directory.

Subdirectories can also be searched recursively.

#### Signature

```php
public static function getFilesInfo(
    string $directory,
    bool $onlyTopLevel = true,
    bool $recursion = false
): array
```

#### Parameters

* `$directory` — Directory to inspect.
* `$onlyTopLevel` — Controls whether subdirectories are traversed.
* `$recursion` — Internal recursion state. Normally left at its default value.

#### Return Value

Returns an associative array indexed by filename.

Each file entry contains the information returned by `FileHelper::info()` together with a `relative_path` value indicating the directory in which the file was found.

Hidden entries are ignored.

#### Examples

```php
$files = DirectoryHelper::getFilesInfo('/var/www/files');
```

Search recursively:

```php
$files = DirectoryHelper::getFilesInfo('/var/www/files', false);
```

---

### `deleteFiles()`

Deletes the contents of a directory recursively.

Subdirectories can optionally be removed as well.

#### Signature

```php
public static function deleteFiles(
    string $directory,
    bool $alsoDeleteDirectory = false,
    bool $htdocs = false,
    int $level = 0
): bool
```

#### Parameters

* `$directory` — Directory whose contents should be deleted.
* `$alsoDeleteDirectory` — Removes descendant directories when `true`.
* `$htdocs` — Preserves `.htaccess`, `index.html`, `index.htm`, `index.php` and `web.config` files when `true`.
* `$level` — Internal recursion level. Normally left at its default value.

#### Return Value

Returns `true` when the directory can be opened and processed.

When `$alsoDeleteDirectory` is `true`, descendant directories are removed, while the directory passed to the initial call is preserved.

Returns `false` when the directory cannot be opened.

#### Examples

Delete files while keeping directories:

```php
DirectoryHelper::deleteFiles('/var/www/cache');
```

Delete files and descendant directories:

```php
DirectoryHelper::deleteFiles('/var/www/cache', true);
```

Preserve common web server files:

```php
DirectoryHelper::deleteFiles('/var/www/public', false, true);
```
