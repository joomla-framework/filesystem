<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\Clients;

use Joomla\Filesystem\Clients\FtpClient;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Filesystem\Clients\FtpClient.
 */
class FtpClientTest extends TestCase
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
     * Skips the test if the FTP extension is not available.
     *
     * @return  void
     */
    private function requireNativeMode(): void
    {
        class_exists(FtpClient::class);

        if (!FTP_NATIVE) {
            $this->markTestSkipped('The FTP extension is not available.');
        }
    }

    /**
     * Test that a native connection is detected, reused and closed.
     */
    public function testNativeConnectionIsReusedAndClosed()
    {
        $this->requireNativeMode();
        [$host, $port] = $this->startServer();

        $client = new FtpClient(['timeout' => 2]);
        $client->connect($host, $port);

        $this->assertTrue($client->isConnected());

        // Already connected, so this must not open a second control connection
        $client->connect($host, $port);
        $client->quit();

        $this->assertFalse($client->isConnected());
        $this->assertSame(['CONNECT', 'QUIT'], $this->stopServer());
    }

    /**
     * Test that getInstance() does not reconnect and log in again for an existing native connection.
     */
    public function testGetInstanceReusesNativeConnection()
    {
        $this->requireNativeMode();
        [$host, $port] = $this->startServer();

        $client = FtpClient::getInstance($host, $port, ['timeout' => 2], 'user', 'secret');

        $this->assertSame($client, FtpClient::getInstance($host, $port, ['timeout' => 2], 'user', 'secret'));

        $client->quit();

        $this->assertSame(['CONNECT', 'USER user', 'PASS secret', 'QUIT'], $this->stopServer());
    }

    /**
     * Test that the destructor closes a native connection.
     */
    public function testDestructorClosesNativeConnection()
    {
        $this->requireNativeMode();
        [$host, $port] = $this->startServer();

        $client = new FtpClient(['timeout' => 2]);
        $client->connect($host, $port);
        unset($client);

        $this->assertSame(['CONNECT', 'QUIT'], $this->stopServer());
    }
}
