# Filesystem package

TODO

## Applying patches

`Patcher::apply()` takes the file names from the `---`/`+++` headers of the patch, removes the number
of leading directories given as `$strip` and appends the rest to the root path passed to `add()` or
`addFile()`. A header that would leave the root is rejected with a `RuntimeException` before any file
is written or deleted: a `..` segment, a stream wrapper such as `php://`, or an absolute path when no
root path was given.
