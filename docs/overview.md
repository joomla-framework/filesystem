# Filesystem package

TODO

## Resuming FTP transfers

`FtpClient::restart($point)` sends `REST <point>` so that the next transfer starts at that byte,
with and without the FTP extension, and throws a `FilesystemException` unless the server confirms
with `350`.
