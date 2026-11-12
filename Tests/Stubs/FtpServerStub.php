<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/*
 * A minimal FTP control channel server for the FtpClient tests.
 *
 * It listens on a random port of 127.0.0.1, prints "host:port" as its first line, then logs every
 * connection ("CONNECT") and every command it receives on STDOUT, one per line. Every command is
 * answered with a plausible reply. The command XSHUTDOWN, or 20 seconds without it, ends the server.
 */

$server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);

if ($server === false) {
    fwrite(STDOUT, "ERROR $errstr\n");

    exit(1);
}

fwrite(STDOUT, stream_socket_get_name($server, false) . "\n");

$clients  = [];
$deadline = time() + 20;

while (time() < $deadline) {
    $read   = array_merge([$server], $clients);
    $write  = null;
    $except = null;

    if (stream_select($read, $write, $except, 0, 200000) < 1) {
        continue;
    }

    foreach ($read as $socket) {
        if ($socket === $server) {
            $client = stream_socket_accept($server);
            fwrite($client, "220 Ready\r\n");
            $clients[(int) $client] = $client;
            fwrite(STDOUT, "CONNECT\n");

            continue;
        }

        $line = fgets($socket);

        if ($line === false) {
            unset($clients[(int) $socket]);
            fclose($socket);

            continue;
        }

        $line = rtrim($line, "\r\n");
        fwrite(STDOUT, $line . "\n");

        switch (strtoupper((string) strtok($line, ' '))) {
            case 'XSHUTDOWN':
                exit(0);

            case 'USER':
                fwrite($socket, "331 Password required\r\n");
                break;

            case 'PASS':
                fwrite($socket, "230 Logged in\r\n");
                break;

            case 'REST':
                fwrite($socket, "350 Restarting\r\n");
                break;

            case 'SITE':
                fwrite($socket, "500 Unknown SITE command\r\n");
                break;

            case 'QUIT':
                fwrite($socket, "221 Bye\r\n");
                unset($clients[(int) $socket]);
                fclose($socket);
                break;

            default:
                fwrite($socket, "200 OK\r\n");
                break;
        }
    }
}
