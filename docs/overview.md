# Filesystem package

TODO

## Upload size limits

`Helper::getFileUploadMaxSize()` returns the smaller of `upload_max_filesize` and `post_max_size`
in bytes, or `0` when neither limits the size. `Helper::getFileUploadMaxSizeWithUnit()` returns the
same value rounded to a unit for display, using `Helper::parseBytesUnit()`:

```php
Helper::parseBytesUnit(2048);              // '2k'
Helper::parseBytesUnit(8 * 1024 ** 3);     // '8G'
Helper::parseBytesUnit(0);                 // '0' (no limit)
```

The largest unit is `T`; bigger values are expressed in it (`1024T` for 1 PiB).
