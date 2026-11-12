# Updating from v2 to v3

Release 3.0.0 raises the PHP requirement, reformats the codebase to PSR-12, and makes one
signature change that can break callers.

## At a glance

| | v2 (2.0.x) | v3 (3.0.0) |
|---|---|---|
| PHP | `^7.2.5 \| ~8.0.0 \| ~8.1.0` | `^8.1.0` |
| `File::write()` `$buffer` | by reference (`&$buffer`) | by value |
| `File::getExt()` | — | new |
| Coding style | Joomla Coding Standard | PSR-12 |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.1** or newer.

## `File::write()` no longer takes its buffer by reference

```php
// v2
public static function write($file, &$buffer, $useStreams = false, $appendToFile = false)

// v3
public static function write($file, $buffer, $useStreams = false, $appendToFile = false)
```

The parameter was never modified inside the method, so the visible behaviour is the same — but a
reference parameter cannot be given a literal. Calls that previously had to go through a variable
now work directly:

```php
// v2 - a fatal error, the argument must be a variable
File::write($path, 'hello');

// v2 - the workaround
$buffer = 'hello';
File::write($path, $buffer);

// v3 - both forms work
File::write($path, 'hello');
```

Nothing in the old calling style breaks. The change matters only if you overrode `write()` in a
subclass: keep the `&` there and PHP reports an incompatible signature.

## `File::getExt()` added

```php
File::getExt('archive.tar.gz');     // 'gz'
```

The counterpart `File::stripExt()` already existed in 2.x. If you had defined your own `getExt()`
helper on a subclass of `File`, check that its signature and return value match.

## Codebase converted to PSR-12

Every file was reformatted from the Joomla Coding Standard to PSR-12, which is why the diff between
2.0.2 and 3.0.0 touches practically every line. Some long signatures — `Stream::open()`,
`Folder::files()`, `Folder::folders()` — are now written across multiple lines. The parameters are
unchanged.

## Dependency changes

| Package | v2 (2.0.x) | v3 (3.0.0) |
|---|---|---|
| `php` | `^7.2.5 \| ~8.0.0 \| ~8.1.0` | `^8.1.0` |

The package has no runtime dependencies beyond PHP itself. Development requirements moved to
PHPUnit `^9.5.28`, `joomla/test` `^3.0`, and the standalone `squizlabs/php_codesniffer` +
`phpstan/phpstan` instead of `joomla/coding-standards`.
