## Updating from v4 to v5

The following changes were made to the Filesystem package between v4 and v5.

### `File::move()` and `Folder::move()` throw instead of returning error messages

`File::move()` and `Folder::move()` reported some failures by returning an error message, which is
truthy, so `if (File::move(...))` treated them as success. This was deprecated in 4.x. They now
throw a `Joomla\Filesystem\Exception\FilesystemException`, as they already did for other failures,
and return only `true`:

| Method | Failure | v4 | v5 |
|---|---|---|---|
| `File::move()` | source file not readable | returns `'Cannot find source file.'` | throws |
| `Folder::move()` | source folder missing | returns `'Cannot find source folder'` | throws |
| `Folder::move()` | destination exists | returns `'Folder already exists'` | throws |
| `Folder::move()` | `rename()` failed | returns `'Rename failed'` | throws |

```php
// v4
$result = Folder::move($src, $dest);

if ($result !== true) {
    echo $result;
}

// v5
try {
    Folder::move($src, $dest);
} catch (FilesystemException $e) {
    echo $e->getMessage();
}
```
