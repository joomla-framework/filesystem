# Filesystem package

TODO

## Scanning uploaded files

`File::isSafeFile($descriptor, $options)`, which `File::upload()` calls unless `$allowUnsafe` is
set, reads the file in chunks of 128 KiB and searches each chunk for `<?php`, `<?`, the phar stub
`__HALT_COMPILER()` and, in archives, the forbidden extensions. Each chunk is searched together
with the end of the previous one, long enough to hold the longest of these strings, so content
that straddles a chunk boundary is found as well.
