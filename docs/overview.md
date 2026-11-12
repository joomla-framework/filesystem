# Filesystem package

TODO

## FTP connections

`Joomla\Filesystem\Clients\FtpClient` uses PHP's FTP extension when it is available and talks to
the server through a socket otherwise. `isConnected()` reports an open control connection in both
modes; `connect()` does nothing if one is open, so `FtpClient::getInstance()` hands out the same
logged-in connection for the same host, port and credentials. `quit()` closes the connection, and
the destructor calls it for a connection that is still open.
