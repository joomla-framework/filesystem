# Filesystem package

TODO

## Paths in error messages

`FilesystemException` passes its message through `Path::removeRoot()`, which replaces the root
directory with `[ROOT]` and the system temporary directory with `[TMP]`. `removeRoot()` takes the
root directory as its second argument and falls back to the `JPATH_ROOT` constant when that is
defined. Without either, only the temporary directory is replaced, so the package can be used
outside the Joomla CMS. Both directories are matched literally, whatever characters they contain,
and with `/` and `\` accepted as separators.
