# Filesystem package

TODO

## Moving files and folders

`File::move($src, $dest)` and `Folder::move($src, $dest)` return `true` on success. Some failures
are reported by returning an error message instead of throwing:

| Method | Returned message |
|---|---|
| `File::move()` | `Cannot find source file.` |
| `Folder::move()` | `Cannot find source folder`, `Folder already exists`, `Rename failed` |

A string is truthy, so `if (File::move(...))` treats these failures as success. Returning the
messages is deprecated and triggers a deprecation notice; 5.0 will throw a `FilesystemException`
instead, as the methods already do for other failures. Compare the result with `true` until then:

```php
if (Folder::move($src, $dest) !== true) {
    // failed
}
```

`Folder::copy()` throws a `FilesystemException` with the message `Destination folder already
exists` when the destination exists and `$force` is not set.
