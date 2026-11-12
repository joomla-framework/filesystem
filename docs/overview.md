# Filesystem package

TODO

## In-memory stream wrappers

Loading `Joomla\Filesystem\Buffer` registers the `buffer://` stream wrapper, and loading
`Joomla\Filesystem\Stream\StringWrapper` the `string://` wrapper, which reads strings registered
with `Joomla\Filesystem\Support\StringController::createRef()`. Both declare the `$context`
property PHP sets on stream wrappers, so they can be used on PHP 8.2 and later without deprecation
notices. A `buffer://` name opens as an empty buffer each time it is opened; the data does not
outlive the handle.
