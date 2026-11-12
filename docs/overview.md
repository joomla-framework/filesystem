# Filesystem package

TODO

## Checking paths

`Path::isBelow($path, $root)` tells whether `$path` is `$root` or lies inside it. Both paths are
cleaned and compared at directory boundaries, so `/var/www/site_backup` is not inside
`/var/www/site`. A path with a `..` segment is never inside. The comparison is lexical (symbolic
links are not followed) and case-insensitive on Windows.

```php
use Joomla\Filesystem\Path;

Path::isBelow('/var/www/site/images', '/var/www/site');   // true
Path::isBelow('/var/www/site_backup', '/var/www/site');   // false
```

`Path::check($path, $basePath)` throws a `FilesystemException` when `$path` contains a `..`
segment or is not inside `$basePath` (the `JPATH_ROOT` constant if no base path is given and the
constant is defined), and returns the cleaned path otherwise. Names that merely contain two dots,
such as `archive..2024.zip`, are accepted.

`Path::find($paths, $file)` only returns a file that lies inside one of the searched directories
after `realpath()` has been applied to both.
