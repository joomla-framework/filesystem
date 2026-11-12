# Filesystem package

TODO

## Safe file names

`File::makeSafe($file, $stripChars = ['#^\.#'])` turns a file name (not a path) into one that is
safe to use: it transliterates non-ASCII characters where possible, removes everything except
letters, digits, `.`, `_`, `-` and spaces, collapses repeated dots, applies the `$stripChars`
patterns (by default removing leading dots) and removes trailing dots. Names longer than 240
characters are shortened to 240, keeping the extension, since most filesystems do not accept
names over 255 bytes and `move_uploaded_file()` already fails earlier:

```php
File::makeSafe(str_repeat('a', 300) . '.jpeg');   // 235 times 'a' followed by '.jpeg'
```
