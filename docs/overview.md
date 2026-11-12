# Overview

The Filesystem package wraps file and directory operations, path handling, a stream abstraction
with support for FTP and custom wrappers, and a unified-diff patcher.

```bash
composer require joomla/filesystem
```

## What is in the package

| Class | Purpose |
|---|---|
| `Joomla\Filesystem\File` | Read, write, copy, move, delete, upload files |
| `Joomla\Filesystem\Folder` | Create, copy, move, delete directories; list contents |
| `Joomla\Filesystem\Path` | Clean, resolve and check paths; read and set permissions |
| `Joomla\Filesystem\Stream` | A stream object with filters, contexts and FTP support |
| `Joomla\Filesystem\Helper` | Remote file size, transports, upload limits |
| `Joomla\Filesystem\Patcher` | Apply unified diffs |
| `Joomla\Filesystem\Buffer` | An in-memory stream wrapper |

All of `File`, `Folder` and `Path` are static-only.

## Files

```php
use Joomla\Filesystem\File;

File::write('/path/to/file.txt', $contents);
File::read('/path/to/file.txt');            // via file_get_contents()
File::copy($src, $dest);
File::move($src, $dest);
File::delete('/path/to/file.txt');          // accepts an array too
File::exists('/path/to/file.txt');

File::getExt('archive.tar.gz');             // 'gz'
File::stripExt('archive.tar.gz');           // 'archive.tar'
File::makeSafe('my file (1).txt');          // 'myfile1.txt'

File::upload($_FILES['x']['tmp_name'], '/uploads/target.txt');
```

Failures raise `Joomla\Filesystem\Exception\FilesystemException`.

## Directories

```php
use Joomla\Filesystem\Folder;

Folder::create('/path/to/dir', 0755);
Folder::copy($src, $dest, '', true);        // $force
Folder::move($src, $dest);
Folder::delete('/path/to/dir');             // recursive
Folder::exists('/path/to/dir');

Folder::files('/path/to/dir', '\.txt$', true, true);    // filter, recurse, full path
Folder::folders('/path/to/dir', '.', false, false);
```

`files()` and `folders()` take a **regular expression** as the filter, not a glob:

```php
Folder::files($dir, '\.(jpe?g|png)$', false, true);
```

## Paths

```php
use Joomla\Filesystem\Path;

Path::clean('/a//b\\c');                    // normalises separators, collapses doubles
Path::resolve('/a/b/../c');                 // '/a/c' - lexical, no filesystem access
Path::check($path, $basePath);              // throws when the path escapes $basePath
Path::isOwner($path);
Path::getPermissions($path);                // 'rwxr-xr-x'
Path::setPermissions($path, '0644', '0755');
Path::canChmod($path);
```

`resolve()` throws a `FilesystemException` when the path climbs above its own root.

## Streams

`Stream` wraps a PHP stream with an object API, and adds context options, filters and a FTP/native
switch:

```php
use Joomla\Filesystem\Stream;

$stream = new Stream();
$stream->open('/path/to/large.csv', 'r');

while (($line = $stream->gets()) !== '') {
    // …
}

$stream->close();
```

`gets()` and `read()` return an **empty string** once the file is exhausted, and raise a
`FilesystemException` only for a genuine failure. That is what lets a stream be consumed in a loop:

```php
$stream->open('/path/to/archive.gz');
$stream->set('processingmethod', 'gz');

do {
    $chunk = $stream->read(8192);
    // …
} while ($chunk);
```

`processingmethod` selects how the bytes are read: `f` for a plain file, `gz` for gzip, `bz` for
bzip2. Set it before `open()`, or pass `$detectprocessingmode = true` to `open()` and let the file
extension decide. A path naming a real stream wrapper — `php://`, `string://` — always falls back
to `f`, because the wrapper does its own decoding.

Filters can be attached per stream:

```php
$stream->appendFilter('convert.iconv.ISO-8859-1/UTF-8');
```

## Patching

```php
use Joomla\Filesystem\Patcher;

$patcher = Patcher::getInstance();
$patcher->addFile('/path/to/changes.patch', '/path/to/project', 1);   // strip 1 component
$applied = $patcher->apply();
```

`apply()` returns the number of files changed and throws on a context mismatch.

## Things to know before you build on this

**`Path::check()` compares path prefixes.** The containment test is a string prefix comparison, not
a path-boundary comparison, so a sibling directory whose name starts with the same characters is
accepted:

| Base path | Checked path | Result |
|---|---|---|
| `/var/www/site` | `/var/www/site/x` | allowed, correctly |
| `/var/www/site` | `/var/www/site_backup/x` | **allowed, incorrectly** |

Its first test is also a plain `strpos($path, '..')`, which rejects legitimate names such as
`archive..2024.zip` while doing nothing about encoded traversal. If a path is built from user
input, resolve it yourself and compare against the real base:

```php
$base = realpath($baseDir);
$real = realpath($candidate);

if ($real === false || !str_starts_with($real, $base . DIRECTORY_SEPARATOR)) {
    throw new \RuntimeException('Path escapes the base directory');
}
```

**`Path::clean()` does not remove `..`.** It only normalises separators, despite the name. Use
`Path::resolve()` when you need dot segments gone.

**`Patcher` does not confine its writes.** With an explicit strip count it removes exactly that many
leading components, so a patch header naming `../../../etc/cron.d/job` produces a path outside the
root, and nothing checks it afterwards. Only apply patches you trust, or verify each target path
against the root before calling `apply()`.

**`Stream::open()` accepts any wrapper.** The filename goes to `fopen()` unfiltered, so `php://`,
`phar://`, `data://` and remote wrappers are all reachable. Check the scheme before opening
anything that came from outside your code.

**`File::delete()` chmods to `0777` first.** The call exists so read-only files can be removed on
Windows, but on Unix it makes the file world-writable for the moment before `unlink()` — and leaves
it that way if the unlink fails.

**`File::upload()` with `$useStreams` skips `move_uploaded_file()`.** Only the default path
verifies that the source really is an uploaded file. Leave `$useStreams` off for uploads unless you
have validated the source yourself.

**`Folder::listFolderTree()`, `Path::removeRoot()` and `Stream::getStream()` need `JPATH_ROOT`.**
All three reference that constant, which belongs to the Joomla CMS rather than the framework. In a
plain framework application it is undefined, and referencing it is an `Error` in PHP 8. For the
stream there is a way round it — the constant is only used to build the path prefixes:

```php
$stream = new Stream();                 // works anywhere
$stream = Stream::getStream(false);     // the same, through the factory
$stream = Stream::getStream();          // needs JPATH_ROOT
```

**Errors are suppressed throughout.** `@chmod`, `@unlink`, `@fopen` and others hide the reason a
call failed, so a `FilesystemException` says what did not work but not why. Check permissions and
paths yourself when diagnosing.
