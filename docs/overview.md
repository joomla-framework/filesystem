# Filesystem package

TODO

## Changing permissions recursively

`Path::setPermissions($path, $filemode = '0644', $foldermode = '0755')` applies `$filemode` to
every file and `$foldermode` to every folder below `$path` (and to `$path` itself). Pass `null` to
leave files or folders unchanged. It returns `false` if any change failed, but carries on with the
remaining entries.
