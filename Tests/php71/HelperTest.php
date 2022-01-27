<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php71;

use Joomla\Filesystem\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Helper.
 *
 * @since  1.0
 */
class HelperTest extends TestCase
{
	/**
	 * Test getSupported method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetSupported(): void
	{
		$this->assertContains(
			'String',
			Helper::getSupported(),
			'Line:' . __LINE__ . ' Joomla Streams must contain String.'
		);

		$registeredStreams = stream_get_wrappers();

		$this->assertCount(
			0,
			array_diff($registeredStreams, Helper::getSupported()),
			'Line:' . __LINE__ . ' getSupported should contains default streams.'
		);
	}

	/**
	 * Test getTransports method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetTransports(): void
	{
		$registeredTransports = stream_get_transports();

		$this->assertCount(
			0,
			array_diff($registeredTransports, Helper::getTransports()),
			'Line:' . __LINE__ . ' getTransports should contains default transports.'
		);
	}

	/**
	 * Test getFilters method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetFilters(): void
	{
		$registeredFilters = stream_get_filters();

		$this->assertCount(
			0,
			array_diff($registeredFilters, Helper::getFilters()),
			'Line:' . __LINE__ . ' getFilters should contains default filters.'
		);
	}

	/**
	 * Test getJStreams method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetJStreams(): void
	{
		$this->assertContains('StringWrapper', Helper::getJStreams());
	}

	/**
	 * Test
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @covers  \Joomla\Filesystem\Helper::isJoomlaStream
	 */
	public function testIsJoomlaStream(): void
	{
		$this->assertTrue(
			Helper::isJoomlaStream('String'),
			'Line:' . __LINE__ . ' String must be a Joomla Stream.'
		);

		$this->assertFalse(
			Helper::isJoomlaStream('unknown'),
			'Line:' . __LINE__ . ' Unkwon is not a Joomla Stream.'
		);
	}
}
