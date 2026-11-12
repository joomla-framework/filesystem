# Filesystem package

TODO

## Cleaning and resolving paths

`Path::clean($path, $ds)` converts all slashes and backslashes to `$ds` and collapses repeated
separators, keeping the leading `\\` of a UNC path when `$ds` is a backslash. It returns an empty
string for an empty or blank path.

`Path::resolve($path)` also removes `.` and `..` segments, without touching the filesystem, and
throws a `FilesystemException` when a `..` would climb above the start of the path. On Windows a
drive (`C:`) counts as that start, so `Path::resolve('C:\..\Windows')` throws instead of returning
the relative path `Windows`. `Path::resolve('')` returns an empty string.
