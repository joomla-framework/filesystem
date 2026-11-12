# Filesystem package

TODO

## Reading strings as streams

The `string://` stream wrapper, registered when `Joomla\Filesystem\Stream\StringWrapper` is
loaded, reads strings registered with `Joomla\Filesystem\Support\StringController::createRef()`:

```php
use Joomla\Filesystem\Support\StringController;

$text = 'abcdefgh';
StringController::createRef('mytext', $text);

$handle = fopen('string://mytext', 'r');
fseek($handle, -3, SEEK_END);
echo stream_get_contents($handle);   // 'fgh'
```

Any registered string can be opened, including an empty string and `"0"`; opening a name that
was not registered fails. `fseek()` works as for files, except that the position cannot be moved
before the start or beyond the end of the string. If the `string` wrapper cannot be registered
because another one already uses that name, loading the class throws a `RuntimeException`.
