# Filesystem package

TODO

## Writing to a stream

`Stream::write($string, $length = 0, $chunk = 0)` writes `$length` bytes of `$string` (all of it
by default) in chunks of `$chunk` bytes (the `chunksize` property, 8192 by default). When the
stream accepts fewer bytes than offered, as non-blocking sockets and some wrappers do, the next
chunk starts right after the last byte written, so nothing is skipped. A write that accepts no
data at all throws a `FilesystemException`.
