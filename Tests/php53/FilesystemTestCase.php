<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php53;

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
	public static function setUpBeforeClass()
	{
		parent::doSetUpBeforeClass();
	}

	/**
	 * @return void
	 */
	protected function setUp()
	{
		parent::doSetUp();
	}

	/**
	 * @return  void
	 */
	protected function tearDown()
	{
		parent::doTearDown();
	}
}
