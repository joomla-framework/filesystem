# Filesystem package

TODO

## Server configuration files in uploads

`File::isSafeFile()`, and therefore `File::upload()`, rejects files named like web server or PHP
configuration files: `.htaccess`, `.htpasswd`, `.user.ini`, `php.ini` and `web.config` (compared
case-insensitively, ignoring trailing dots and spaces). Uploaded into a web accessible directory,
such a file can make the server execute otherwise harmless uploads, for example images. The list
is available as `File::FORBIDDEN_FILE_NAMES` and can be replaced with the `forbidden_filenames`
option; an empty array switches the check off:

```php
File::isSafeFile($descriptor, ['forbidden_filenames' => []]);
```
