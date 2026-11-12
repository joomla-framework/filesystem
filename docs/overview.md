# Filesystem package

TODO

## Deleting files

`File::delete($file)` takes a path or an array of paths. If a file cannot be deleted at first,
for example because it is read-only on Windows, `delete()` adds the write permission for the owner
and tries again; the permissions are never widened beyond that and are restored if the second
attempt fails as well. A failure throws a `FilesystemException` naming the full path of the file,
with the root and temporary directories masked as `[ROOT]` and `[TMP]`.
