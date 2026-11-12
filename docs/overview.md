# Filesystem package

TODO

## FTP transfer types and global constants

Pass the transfer type of `FtpClient` as one of its class constants:

```php
use Joomla\Filesystem\Clients\FtpClient;

$ftp = FtpClient::getInstance($host, 21, ['type' => FtpClient::TYPE_BINARY], $user, $password);
```

| Class constant | Meaning | Deprecated global constant |
|---|---|---|
| `FtpClient::TYPE_AUTOASCII` | ASCII or binary by file extension | `FTP_AUTOASCII` |
| `FtpClient::TYPE_ASCII` | ASCII mode | `FTP_ASCII` (when the FTP extension is not loaded) |
| `FtpClient::TYPE_BINARY` | binary mode | `FTP_BINARY` (when the FTP extension is not loaded) |
| `FtpClient::CRLF` | line ending of FTP commands | `CRLF` |

Loading `FtpClient` defines the global constants `CRLF`, `FTP_AUTOASCII` and `FTP_NATIVE`, and
`FTP_ASCII` and `FTP_BINARY` when the FTP extension does not. These global constants are
deprecated and will no longer be defined in 5.0. Use the class constants above, and
`extension_loaded('ftp')` instead of `FTP_NATIVE`. With the FTP extension loaded, `FTP_ASCII` and
`FTP_BINARY` remain available from the extension itself.
