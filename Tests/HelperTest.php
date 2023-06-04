<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Filesystem\Helper.
 */
class HelperTest extends TestCase
{
    /**
     * Test remotefsize method.
     */
    public function testRemotefsize()
    {
        $this->assertFalse(
            Helper::remotefsize('http://www.joomla.o'),
            'Line:' . __LINE__ . ' for an invalid remote file path, false should be returned.'
        );

        $this->assertTrue(
            is_numeric(Helper::remotefsize('https://www.example.org')),
            'Line:' . __LINE__ . ' for a valid remote file, returned size should be numeric.'
        );

        $this->assertFalse(
            Helper::remotefsize('ftppp://ftp.mozilla.org/index.html'),
            'Line:' . __LINE__ . ' for an invalid remote file path, false should be returned.'
        );

        // Find a more reliable FTP server to test with
        if (false) {
            $this->assertTrue(
                is_numeric(Helper::remotefsize('ftp://ftp.mozilla.org/index.html')),
                'Line:' . __LINE__ . ' for a valid remote file, returned size should be numeric.'
            );
        }
    }

    /**
     * Test ftpChmod method.
     */
    public function testFtpChmod()
    {
        $this->assertFalse(
            Helper::ftpChmod('ftp://ftppp.mozilla.org/index.html', 0777),
            'Line:' . __LINE__ . ' for an invalid remote file, false should be returned.'
        );

        $this->assertFalse(
            Helper::ftpChmod('ftp://ftp.mozilla.org/index.html', 0777),
            'Line:' . __LINE__ . ' for an inaccessible remote file, false should be returned.'
        );
    }

    /**
     * Test getSupported method.
     */
    public function testGetSupported()
    {
        $this->assertContains(
            'StringWrapper',
            Helper::getSupported(),
            'Line:' . __LINE__ . ' Joomla Streams must contain StringWrapper.'
        );

        $registeredStreams = stream_get_wrappers();

        $this->assertEquals(
            \count(array_diff($registeredStreams, Helper::getSupported())),
            0,
            'Line:' . __LINE__ . ' getSupported should contains default streams.'
        );
    }

    /**
     * Test getTransports method.
     */
    public function testGetTransports()
    {
        $registeredTransports = stream_get_transports();

        $this->assertEquals(
            \count(array_diff($registeredTransports, Helper::getTransports())),
            0,
            'Line:' . __LINE__ . ' getTransports should contains default transports.'
        );
    }

    /**
     * Test getFilters method.
     */
    public function testGetFilters()
    {
        $registeredFilters = stream_get_filters();

        $this->assertEquals(
            \count(array_diff($registeredFilters, Helper::getFilters())),
            0,
            'Line:' . __LINE__ . ' getFilters should contains default filters.'
        );
    }

    /**
     * Test getJStreams method.
     */
    public function testGetJStreams()
    {
        $streams = Helper::getJStreams();

        $this->assertTrue(\in_array('StringWrapper', Helper::getJStreams()));
    }

    /**
     * Test
     *
     * @covers  Joomla\Filesystem\Helper::isJoomlaStream
     */
    public function testIsJoomlaStream()
    {
        $this->assertTrue(
            Helper::isJoomlaStream('StringWrapper'),
            'Line:' . __LINE__ . ' StringWrapper must be a Joomla Stream.'
        );

        $this->assertFalse(
            Helper::isJoomlaStream('unknown'),
            'Line:' . __LINE__ . ' Unkwon is not a Joomla Stream.'
        );
    }
}
