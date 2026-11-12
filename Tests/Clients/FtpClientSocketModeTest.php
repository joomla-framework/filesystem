<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\Clients;

use Joomla\Filesystem\Clients\FtpClient;
use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the socket mode of Joomla\Filesystem\Clients\FtpClient, which is used without the FTP extension.
 */
class FtpClientSocketModeTest extends TestCase
{
    /**
     * Sockets to close after the test
     *
     * @var  resource[]
     */
    private $sockets = [];

    /**
     * Clients created by the test
     *
     * @var  FtpClient[]
     */
    private $clients = [];

    /**
     * Closes the sockets opened by the test.
     *
     * @return  void
     */
    protected function tearDown(): void
    {
        // The socket is not an FTP extension connection, so the destructor must not try to close it
        foreach ($this->clients as $client) {
            TestHelper::setValue($client, 'conn', null);
        }

        foreach ($this->sockets as $socket) {
            if (\is_resource($socket)) {
                fclose($socket);
            }
        }

        parent::tearDown();
    }

    /**
     * Creates a client whose control connection is connected to a socket of the test.
     *
     * @return  array  The client and the server side of the control connection
     */
    private function createConnectedClient(): array
    {
        $listener = stream_socket_server('tcp://127.0.0.1:0');
        $conn     = stream_socket_client('tcp://' . stream_socket_get_name($listener, false));
        $server   = stream_socket_accept($listener);

        $this->sockets = array_merge($this->sockets, [$listener, $conn, $server]);

        $client = new FtpClient(['timeout' => 1]);
        TestHelper::setValue($client, 'conn', $conn);
        $this->clients[] = $client;

        return [$client, $server];
    }

    /**
     * Test that a command containing a line break is not sent.
     */
    public function testPutCmdRejectsLineBreaks()
    {
        [$client, $server] = $this->createConnectedClient();

        try {
            TestHelper::invoke($client, '_putCmd', "DELE file.txt\r\nDELE other.txt", 250);
            $this->fail('A command with a line break has been accepted.');
        } catch (FilesystemException $e) {
            $this->assertStringContainsString('line break', $e->getMessage());
        }

        stream_set_blocking($server, false);
        $this->assertSame('', (string) fread($server, 1024), 'Nothing must have been sent to the server.');
    }

    /**
     * Test that the data connection goes to the server of the control connection, not to the address in the PASV reply.
     */
    public function testPassiveIgnoresTheAddressInTheReply()
    {
        [$client, $server] = $this->createConnectedClient();

        $data = stream_socket_server('tcp://127.0.0.1:0');
        $this->sockets[] = $data;
        $port = (int) substr(strrchr(stream_socket_get_name($data, false), ':'), 1);

        fwrite($server, sprintf("227 Entering Passive Mode (10,255,255,1,%d,%d)\r\n", intdiv($port, 256), $port % 256));

        $this->assertTrue(TestHelper::invoke($client, '_passive'));
        $this->assertSame(['ip' => '127.0.0.1', 'port' => $port], TestHelper::getValue($client, 'pasv'));
    }
}
