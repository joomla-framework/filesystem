# Filesystem package

TODO

## FTP transfer types

The transfer type of `Joomla\Filesystem\Clients\FtpClient` is set with the `type` option, using
`FtpClient::TYPE_BINARY` (the default), `FtpClient::TYPE_ASCII` or `FtpClient::TYPE_AUTOASCII`,
which picks ASCII or binary mode by the file extension. The client uses PHP's FTP extension when it
is loaded and sockets otherwise.
