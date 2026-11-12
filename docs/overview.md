# Filesystem package

TODO

## Stream prefixes

`Stream::getStream()` creates a `Stream` whose read and write prefixes are the `JPATH_ROOT`
constant of the Joomla CMS. When that constant is not defined, or when `getStream(false)` is
called, the prefixes are empty and file names are used as given. This is also what `File` and
`Folder` use when called with `$useStreams = true`, so those code paths work outside the CMS.
