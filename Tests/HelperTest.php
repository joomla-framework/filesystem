<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Helper;
use PHPUnit\Framework\Attributes\DataProvider;
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

        /*
         * remotefsize() reads Content-Length, which a server is free not to send: example.org
         * now answers with Transfer-Encoding: chunked. Only assert the size when the remote end
         * actually provides one, otherwise this test measures a third party's configuration.
         */
        $headers = @get_headers('https://www.example.org', 1);

        if (!\is_array($headers) || !\array_key_exists('Content-Length', $headers)) {
            $this->markTestSkipped(
                'The remote host is unreachable or does not send a Content-Length header,'
                . ' so the size of a remote file cannot be checked here.'
            );
        }

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

    /**
     * Test getWriteModes method.
     *
     * @covers  Joomla\Filesystem\Helper::getWriteModes
     */
    public function testGetWriteModes()
    {
        $modes = Helper::getWriteModes();

        $this->assertIsArray($modes);

        foreach (['w', 'w+', 'a', 'a+', 'r+', 'x', 'x+'] as $mode) {
            $this->assertContains($mode, $modes, sprintf('"%s" opens a stream for writing.', $mode));
        }

        $this->assertNotContains('r', $modes, '"r" is read-only and must not be listed.');
        $this->assertNotContains('rb', $modes, 'Binary flags are not part of the mode list.');
    }

    /**
     * Test getFileUploadMaxSize method.
     *
     * @covers  Joomla\Filesystem\Helper::getFileUploadMaxSize
     */
    public function testGetFileUploadMaxSize()
    {
        $maxSize = Helper::getFileUploadMaxSize();

        $this->assertIsNumeric($maxSize);
        $this->assertGreaterThanOrEqual(0, $maxSize);

        // The result is the smaller of the two limits, so it can never exceed either of them.
        foreach (['post_max_size', 'upload_max_filesize'] as $setting) {
            $limit = self::iniValueInBytes(\ini_get($setting));

            if ($limit > 0) {
                $this->assertLessThanOrEqual(
                    $limit,
                    $maxSize,
                    sprintf('The upload limit must not exceed %s.', $setting)
                );
            }
        }
    }

    /**
     * Test getFileUploadMaxSizeWithUnit method.
     *
     * @covers  Joomla\Filesystem\Helper::getFileUploadMaxSizeWithUnit
     */
    public function testGetFileUploadMaxSizeWithUnit()
    {
        $this->assertMatchesRegularExpression(
            '/^\d+(|k|M|G|T)$/',
            Helper::getFileUploadMaxSizeWithUnit(),
            'The value is a rounded number followed by an optional unit.'
        );
    }

    /**
     * Data provider for parseBytesUnit.
     *
     * @return  array
     */
    public static function dataParseBytesUnit(): array
    {
        return [
            'bytes'          => [512, '512'],
            'exactly 1 KiB'  => [1024, '1k'],
            'kilobytes'      => [2048, '2k'],
            'exactly 1 MiB'  => [1024 ** 2, '1M'],
            'megabytes'      => [5 * 1024 ** 2, '5M'],
            'exactly 1 GiB'  => [1024 ** 3, '1G'],
            'exactly 1 TiB'  => [1024 ** 4, '1T'],
            'rounds up'      => [1500, '1k'],
        ];
    }

    /**
     * Test parseBytesUnit method.
     *
     * @param   float   $bytes     The byte count to format.
     * @param   string  $expected  The expected result.
     *
     * @covers  Joomla\Filesystem\Helper::parseBytesUnit
     */
    #[DataProvider('dataParseBytesUnit')]
    public function testParseBytesUnit($bytes, string $expected)
    {
        $this->assertSame($expected, Helper::parseBytesUnit($bytes));
    }

    /**
     * Convert a PHP ini shorthand size to bytes, the way PHP itself does.
     *
     * @param   string  $value  The ini value, e.g. "8M".
     *
     * @return  float
     */
    private static function iniValueInBytes($value): float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        $number = (float) $value;
        $unit   = strtolower(substr($value, -1));

        return match ($unit) {
            'g'     => $number * 1024 ** 3,
            'm'     => $number * 1024 ** 2,
            'k'     => $number * 1024,
            default => $number,
        };
    }
}
