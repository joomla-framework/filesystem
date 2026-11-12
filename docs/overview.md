# Filesystem package

TODO

## Folder trees

`Folder::listFolderTree($path, $filter, $maxLevel = 3)` returns the folders below `$path` as a flat
list for tree displays. Each entry has an `id` (unique within the call), the `id` of its `parent`
(`0` for the top level), the `name`, the `fullname` and a `relname`. The `relname` is the full name
with the `JPATH_ROOT` constant of the Joomla CMS removed, or with `$path` removed when that
constant is not defined.
