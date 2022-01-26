<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php71;

use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Tests\FilesystemTestCase as TestCase;

/**
 * Base test case for filesystem interacting tests
 *
 * @since  1.4.0
 */
class FilesystemTestCase extends TestCase
{
	/**
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::doSetUpBeforeClass();
	}

	/**
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::doSetUp();
	}

	/**
	 * @return  void
	 */
	protected function tearDown(): void
	{
		parent::doTearDown();
	}
}
