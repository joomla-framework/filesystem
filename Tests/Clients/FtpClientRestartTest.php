<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\Clients;

use Joomla\Filesystem\Clients\FtpClient;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Joomla\Filesystem\Clients\FtpClient::restart().
 */
class FtpClientRestartTest extends TestCase
{
    /**
     * The process of the FTP server stub
     *
     * @var  resource|null
     */
    private $server;

    /**
     * The pipes of the FTP server stub process
     *
     * @var  array
     */
    private $pipes = [];

    /**
     * The address of the FTP server stub, "host:port"
     *
     * @var  string
     */
    private $address = '';

    /**
     * Stops the FTP server stub, if it is still running.
     *
     * @return  void
     */
    protected function tearDown(): void
    {
        $this->stopServer();

        parent::tearDown();
    }

    /**
     * Starts the FTP server stub in a separate process.
     *
     * @return  array  The host and the port of the server
     */
    private function startServer(): array
    {
        $this->server  = proc_open([PHP_BINARY, \dirname(__DIR__) . '/Stubs/FtpServerStub.php'], [1 => ['pipe', 'w']], $this->pipes);
        $this->address = trim((string) fgets($this->pipes[1]));

        if (!preg_match('/^[0-9.]+:[0-9]+$/', $this->address)) {
            $this->stopServer();
            $this->markTestSkipped('The FTP server stub could not be started.');
        }

        [$host, $port] = explode(':', $this->address);

        return [$host, (int) $port];
    }

    /**
     * Stops the FTP server stub and returns what it logged.
     *
     * @return  string[]  The connections ("CONNECT") and commands the server received
     */
    private function stopServer(): array
    {
        if ($this->server === null) {
            return [];
        }

        $socket = @stream_socket_client('tcp://' . $this->address, $errno, $errstr, 2);

        if ($socket !== false) {
            fgets($socket);
            fwrite($socket, "XSHUTDOWN\r\n");
            fclose($socket);
        }

        $log = stream_get_contents($this->pipes[1]);
        fclose($this->pipes[1]);
        proc_close($this->server);
        $this->server = null;

        // Drop the shutdown connection
        return \array_slice(explode("\n", trim($log)), 0, -2);
    }

    /**
     * Test that restart() sends REST in native mode.
     */
    public function testRestartInNativeMode()
    {
        class_exists(FtpClient::class);

        if (!FTP_NATIVE) {
            $this->markTestSkipped('The FTP extension is not available.');
        }

        [$host, $port] = $this->startServer();

        $client = new FtpClient(['timeout' => 2]);
        $client->connect($host, $port);

        try {
            $this->assertTrue($client->restart(100));
        } finally {
            // Close the connection directly, the destructor may not see it
            ftp_close(TestHelper::getValue($client, 'conn'));
            TestHelper::setValue($client, 'conn', null);
        }

        $this->assertSame(['CONNECT', 'REST 100', 'QUIT'], $this->stopServer());
    }
}
