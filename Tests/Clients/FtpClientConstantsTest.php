<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\Clients;

use Joomla\Filesystem\Clients\FtpClient;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the constants of Joomla\Filesystem\Clients\FtpClient.
 */
class FtpClientConstantsTest extends TestCase
{
    /**
     * Test that loading the client does not define global constants.
     */
    public function testNoGlobalConstantsAreDefined()
    {
        $script = tempnam(sys_get_temp_dir(), 'jfs');
        file_put_contents(
            $script,
            '<?php
            require ' . var_export(\dirname(__DIR__, 2) . '/vendor/autoload.php', true) . ';
            class_exists(Joomla\Filesystem\Clients\FtpClient::class);
            foreach (["CRLF", "FTP_AUTOASCII", "FTP_NATIVE"] as $name) {
                echo $name, "=", var_export(defined($name), true), " ";
            }'
        );

        $process = proc_open([PHP_BINARY, $script], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $output  = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        unlink($script);

        $this->assertSame('CRLF=false FTP_AUTOASCII=false FTP_NATIVE=false ', $output);
    }

    /**
     * Test that the transfer types have the values of the FTP extension, which receives them in native mode.
     */
    public function testTransferTypesMatchTheFtpExtension()
    {
        if (!\extension_loaded('ftp')) {
            $this->markTestSkipped('The FTP extension is not available.');
        }

        $this->assertSame(\FTP_ASCII, FtpClient::TYPE_ASCII);
        $this->assertSame(\FTP_BINARY, FtpClient::TYPE_BINARY);
    }
}
