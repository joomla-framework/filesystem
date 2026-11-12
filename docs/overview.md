# Filesystem package

TODO

## Creating and listing folders

`Folder::create($path, $mode = 0755)` creates a folder and any missing parent folders. If another
process creates the same folder at the same time, `create()` still succeeds; it only throws a
`FilesystemException` when the folder does not exist afterwards.

`Folder::files()` and `Folder::folders()` with `$recurse` follow symbolic links (and junctions on
Windows) to folders, but not a link back to a folder they are already reading, so a link loop is
listed once instead of being followed until the path gets too long.
