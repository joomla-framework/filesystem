## Updating from v3 to v4

The following changes were made to the Filesystem package between v3 and v4.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.3 or newer.

### `StringController::_getArray()` was removed

The deprecated method `StringController::_getArray()` was removed. Use `StringController::getArray()` instead.

### Docblock review with subsequent fixes to parameter types

During a review of the docblocks, several types for parameters were corrected. While most changes simply allowed parameters to be nullable, the following fixes change the basic type of the parameter:

* `Patcher::findHunk()` and `Patcher::applyHunk()` got the parameters `$srcLine`, `$srcSize`, `$dstLine` and `$dstSize` changed from type `string` to `integer`. These methods already expected integer values, but now the docblock also correctly states that.
* `Stream::$context` now correctly states that it expects a `resource` and not a `string`
* `Stream::getFileHandle()` now correctly states that it returns a (nullable) `resource` instead of a (non-existent) `File` object.
* `Stream\StringWrapper::$len` now correctly states that it is an `integer` and not a `string`.
