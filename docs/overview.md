# Filesystem package

TODO

## Patcher is deprecated

`Joomla\Filesystem\Patcher`, which applies unified diffs, is deprecated and will be removed in
5.0 without replacement; `Patcher::getInstance()` triggers a deprecation notice. Neither the
framework nor the Joomla CMS uses it. Apply patches with `git apply` or `patch` instead.
