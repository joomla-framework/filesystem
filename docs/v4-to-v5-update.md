## Updating from v4 to v5

The following changes were made to the Filesystem package between v4 and v5.

### `Patcher` was removed

The `Joomla\Filesystem\Patcher` class, which applied unified diffs, was deprecated in 4.x and has
been removed without replacement. Neither the framework nor the Joomla CMS used it. Apply patches
with `git apply` or `patch` instead.
