## Updating from v4 to v5

The following changes were made to the Filesystem package between v4 and v5.

### `FtpClient` no longer defines global constants

Loading `Joomla\Filesystem\Clients\FtpClient` used to define the global constants `CRLF`,
`FTP_AUTOASCII` and `FTP_NATIVE`, and `FTP_ASCII` and `FTP_BINARY` when the FTP extension was not
loaded. These were deprecated in 4.x and are no longer defined. Use the class constants instead:

| v4 global constant | v5 replacement |
|---|---|
| `CRLF` | `FtpClient::CRLF` |
| `FTP_AUTOASCII` | `FtpClient::TYPE_AUTOASCII` |
| `FTP_ASCII` | `FtpClient::TYPE_ASCII` |
| `FTP_BINARY` | `FtpClient::TYPE_BINARY` |
| `FTP_NATIVE` | `extension_loaded('ftp')` |

```php
// v4
$ftp = FtpClient::getInstance($host, 21, ['type' => FTP_BINARY]);

// v5
$ftp = FtpClient::getInstance($host, 21, ['type' => FtpClient::TYPE_BINARY]);
```

`FtpClient::TYPE_ASCII` and `FtpClient::TYPE_BINARY` have the values of the FTP extension's
`FTP_ASCII` and `FTP_BINARY`, so code passing the extension's constants keeps working when the
extension is loaded. Without the extension, `FTP_ASCII` and `FTP_BINARY` are no longer defined.
