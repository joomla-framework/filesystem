# Filesystem package

TODO

## Verifying patches

Before `Patcher::apply()` changes a file, it compares every context and removed line of each hunk
with the file. The comparison is exact (no numeric comparison of strings such as `1000` and
`1e3`), and a hunk that refers to lines beyond the end of the file fails as well. Either case
throws a `RuntimeException` ("Failed source verification") and no file is written.
