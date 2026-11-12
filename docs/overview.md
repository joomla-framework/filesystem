# Filesystem package

TODO

## File size of a stream

`Stream::filesize()` returns the size of the opened file in bytes, `0` for an empty file. For
remote files it falls back to the `Content-Length` header (HTTP) or the `SIZE` command (FTP), and
it throws a `FilesystemException` when neither works.
