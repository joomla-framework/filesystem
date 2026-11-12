# Filesystem package

TODO

## Moving files and folders

`File::move($src, $dest)` and `Folder::move($src, $dest)` return `true` on success and throw a
`Joomla\Filesystem\Exception\FilesystemException` on failure, for example when the source does
not exist or, for `Folder::move()`, when the destination folder exists.
