# Filesystem package

TODO

## File mode after writing through a stream

After a `Stream` opened for writing is closed, and in `Stream::writeFile()`, the file gets the
stream's file mode via `Stream::chmod()`. For plain files and `ftp://`/`ftps://` paths a failure
raises a `FilesystemException`. Other stream wrappers, such as `php://memory` or `http://`, often
cannot change modes at all, so a failure is ignored for them. The destructor of `Stream` closes a
stream that is still open and never throws.
