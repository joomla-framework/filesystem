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
     * Test that the class constants have the values of the deprecated global constants.
     */
    public function testClassConstantsMatchTheGlobalConstants()
    {
        // Loading the class defines the global constants
        class_exists(FtpClient::class);

        $this->assertSame(\FTP_AUTOASCII, FtpClient::TYPE_AUTOASCII);
        $this->assertSame(\FTP_ASCII, FtpClient::TYPE_ASCII);
        $this->assertSame(\FTP_BINARY, FtpClient::TYPE_BINARY);
        $this->assertSame(\CRLF, FtpClient::CRLF);
        $this->assertNotSame(FtpClient::TYPE_ASCII, FtpClient::TYPE_BINARY);
    }
}
