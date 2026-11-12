# Filesystem package

TODO

## Disabling the extension check of uploads

`File::isSafeFile()` accepts `'forbidden_extensions' => []` to allow any file extension. Only the
extension checks are switched off: the content checks still run, and the short tag scan still
applies to the file types listed in `shorttag_extensions`.
