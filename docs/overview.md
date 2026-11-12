# Filesystem package

TODO

## FTP commands and passive mode

Without the FTP extension, `Joomla\Filesystem\Clients\FtpClient` sends the commands itself. A
command whose arguments (paths, user names, modes) contain a line break or a null byte is
rejected with a `FilesystemException` instead of being sent, as the rest of the argument would
otherwise run as further commands. For passive mode transfers the client connects to the address
of the server it is talking to and takes only the port from the server's `PASV` reply, so a
server cannot direct the data connection to another host.
