# Updating from v3 to v4

Release 4.0.0 raises the PHP requirement and removes one long-deprecated method.

## At a glance

| | v3 (3.1.1) | v4 (4.0.0) |
|---|---|---|
| PHP | `^8.1.0` | `^8.3.0` |
| `StringController::_getArray()` | deprecated since 2.0 | **removed** |
| `Stream::read()` / `gets()` at end of file | raised a `FilesystemException` | return an empty string |
| `Stream::open()` with an absolute Windows path | discarded the processing method | keeps it |
| Everything else | — | unchanged |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.3** or newer.

## `Stream` can be read to the end

Reading a stream to the end used to be impossible without catching an exception. Both `read()` and
`gets()` decided that a falsy result meant failure, and every read function reports the end of the
file with an empty string — so the read that reached it raised `Unable to read from stream`:

```php
// v3 - the loop can only be left through the exception
do {
    $chunk = $stream->read(8192);
} while ($chunk);

// v4 - read() returns '' at the end and the loop ends by itself
do {
    $chunk = $stream->read(8192);
} while ($chunk);
```

The same test also called a chunk holding nothing but the character `0` a failure, since `"0"` is
falsy in PHP. A one byte file containing `0` could not be read at all.

`read()` now treats `false` as a failure and an empty string as the end of the file. `gets()` gets
the same distinction, except that `fgets()` reports both with `false`, so the stream position
settles it: at the end of the file `gets()` returns `''`, elsewhere it still raises.

If you catch `FilesystemException` around a read loop to detect the end of the stream, that catch
is now dead code — the loop ends on its own.

## `Stream::open()` no longer mistakes a drive letter for a stream wrapper

`open()` ran the file name through `parse_url()` and, on finding a scheme, forced the processing
method to `f` on the grounds that a wrapper does its own decoding. On Windows `parse_url()` reports
the drive letter of `D:\path\file.gz` as the scheme `D`, so an absolute path silently discarded the
caller's setting:

```php
$stream->set('processingmethod', 'gz');
$stream->open('D:\backups\dump.sql.gz');

// v3: reads the compressed bytes, because the method was reset to 'f'
// v4: reads the decompressed contents
```

A scheme of a single character is now ignored, since no registered stream wrapper has a one
character name. Real wrappers — `php://`, `string://` — behave as before.

## `StringController::_getArray()` removed

```php
// v3
$strings = \Joomla\Filesystem\Support\StringController::_getArray();

// v4
$strings = \Joomla\Filesystem\Support\StringController::getArray();
```

It had been a deprecated alias for `getArray()` since 2.0 and did nothing else. The class is
internal plumbing for the `string://` stream wrapper, so most applications never touched it.

```bash
grep -rn "_getArray" src/
```

## No other API changes

`File`, `Folder`, `Path`, `Stream`, `Helper`, `Patcher` and `Buffer` have the same signatures in
4.0.0 as in 3.1.1. The remaining changes are PHP 8.3 compatibility fixes with no visible effect:

* `Stream::open()` passes an `int` to `gzopen()`'s third parameter instead of a `bool`
* `Patcher::apply()` uses a truthiness check where it previously used `isset()` on a variable that
  is always set
* nullable docblock annotations (`?string $filemode`) added to `Path::setPermissions()` and others

## Dependency changes

| Package | v3 (3.1.1) | v4 (4.0.0) |
|---|---|---|
| `php` | `^8.1.0` | `^8.3.0` |

Still no runtime dependencies beyond PHP. Development requirements moved to PHPUnit `^12.2.7` and
`joomla/test` `^4.0`; if you run this package's test suite, note that PHPUnit 12 ignores
doc-comment metadata such as `@dataProvider` — use attributes instead.
