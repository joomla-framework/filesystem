<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php71;

use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;

/**
 * Tests for the Path class.
 *
 * @since  1.0
 */
class PathTest extends FilesystemTestCase
{
	/**
	 * Test canChmod method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testCanChmodFile(): void
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			Path::canChmod($this->testPath . '/' . $name)
		);
	}

	/**
	 * Test canChmod method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testCanChmodFolder(): void
	{
		$this->skipIfUnableToChmod();

		$this->assertTrue(
			Path::canChmod($this->testPath)
		);
	}

	/**
	 * Test canChmod method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testCanChmodNonExistingFile(): void
	{
		$this->skipIfUnableToChmod();

		$this->assertFalse(
			Path::canChmod($this->testPath . '/tempFile')
		);
	}

	/**
	 * Test setPermissions method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testSetAndGetPermissionsFile(): void
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		// The parent test case sets umask(0) therefore we are creating files with 0666 permissions
		$this->assertSame(
			'rw-rw-rw-',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		$this->assertTrue(
			Path::setPermissions($this->testPath . '/' . $name, '0644')
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rw-r--r--',
			Path::getPermissions($this->testPath . '/' . $name)
		);
	}

	/**
	 * Test setPermissions method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testSetAndGetPermissionsFolder(): void
	{
		$this->skipIfUnableToChmod();

		// The parent test case sets umask(0) therefore we are creating folders with 0777 permissions
		$this->assertSame(
			'rwxrwxrwx',
			Path::getPermissions($this->testPath)
		);

		$this->assertTrue(
			Path::setPermissions($this->testPath, null, '0755')
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rwxr-xr-x',
			Path::getPermissions($this->testPath)
		);
	}

	/**
	 * Test setPermissions method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testSetAndGetPermissionsFolderWithFiles(): void
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		// The parent test case sets umask(0) therefore we are creating files with 0666 permissions
		$this->assertSame(
			'rw-rw-rw-',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		// The parent test case sets umask(0) therefore we are creating folders with 0777 permissions
		$this->assertSame(
			'rwxrwxrwx',
			Path::getPermissions($this->testPath)
		);

		$this->assertTrue(
			Path::setPermissions($this->testPath, '0644', '0755')
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rw-r--r--',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		$this->assertSame(
			'rwxr-xr-x',
			Path::getPermissions($this->testPath)
		);
	}

	/**
	 * Test data for check method.
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function dataCheckValidPaths(): array
	{
		return array(
			array('/var/foo'),
			array('/var/foo/bar'),
			array('/var/fo.o/bar'),
			array('/var/./bar'),
		);
	}

	/**
	 * Test checkValidPaths method.
	 *
	 * @param  string  $data  Path to check for valid
	 *
	 * @return  void
	 *
	 * @dataProvider dataCheckValidPaths
	 * @since   1.4.0
	 */
	public function testCheckValidPaths($data): void
	{
		if (DIRECTORY_SEPARATOR === '\\')
		{
			$this->markTestSkipped('Checking paths is not supported on Windows');
		}

		$this->assertEquals(
			Path::clean(__DIR__ . $data),
			Path::check(__DIR__ . $data)
		);
	}

	/**
	 * Test data for check method exception.
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function dataCheckExceptionPaths(): array
	{
		return array(
			array('../var/foo/bar'),
			array('/var/../foo/bar'),
			array('/var/foo../bar'),
			array('/var/foo/..'),
			array('/var/foo..bar'),
			array('/var/foo/..bar'),
			array('/var/foo/bar..'),
			array('/var/..foo./bar'),
		);
	}

	/**
	 * Test exceptions in check method.
	 *
	 * @param  string  $data  Paths to check.
	 *
	 * @return  void
	 *
	 * @dataProvider dataCheckExceptionPaths
	 *
	 * @since   1.4.0
	 */
	public function testCheckExceptionPaths($data): void
	{
		$this->expectException(FilesystemException::class);
		Path::check(__DIR__ . $data);
	}

	/**
	 * Data provider for testClean() method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getCleanData(): array
	{
		return array(
			// Input Path, Directory Separator, Expected Output
			'Nothing to do.' => array('/var/www/foo/bar/baz', '/', '/var/www/foo/bar/baz'),
			'Return JPATH_ROOT.' => array(' ', '/', JPATH_ROOT),
			'One backslash.' => array('/var/www/foo\\bar/baz', '/', '/var/www/foo/bar/baz'),
			'Two and one backslashes.' => array('/var/www\\\\foo\\bar/baz', '/', '/var/www/foo/bar/baz'),
			'Mixed backslashes and double forward slashes.' => array('/var\\/www//foo\\bar/baz', '/', '/var/www/foo/bar/baz'),
			'UNC path.' => array('\\\\www\\docroot', '\\', '\\\\www\\docroot'),
			'UNC path with forward slash.' => array('\\\\www/docroot', '\\', '\\\\www\\docroot'),
			'UNC path with UNIX directory separator.' => array('\\\\www/docroot', '/', '/www/docroot'),
		);
	}

	/**
	 * Tests the clean method.
	 *
	 * @param  string  $input     Input Path
	 * @param  string  $ds        Directory Separator
	 * @param  string  $expected  Expected Output
	 *
	 * @return  void
	 *
	 * @covers        \Joomla\Filesystem\Path::clean
	 * @dataProvider  getCleanData
	 * @since      1.0
	 */
	public function testClean($input, $ds, $expected): void
	{
		$this->assertEquals(
			$expected,
			Path::clean($input, $ds)
		);
	}

	/**
	 * Tests the JPath::clean method with an array as an input.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testCleanArrayPath(): void
	{
		$this->expectException(\UnexpectedValueException::class);
		Path::clean(array('/path/to/folder'));
	}

	/**
	 * Test isOwner method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testIsOwner(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			Path::isOwner($this->testPath . '/' . $name)
		);
	}

	/**
	 * Test find method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testFind(): void
	{
		$this->assertFalse(
			Path::find(dirname(__DIR__), 'PathTest.php')
		);

		$this->assertEquals(
			__FILE__,
			Path::find(__DIR__, 'PathTest.php')
		);
	}

	/**
	 * Test resolve method
	 *
	 * @param  string  $path            test path
	 * @param  string  $expectedResult  expected path
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 *
	 * @dataProvider  getResolveData
	 */
	public function testResolve($path, $expectedResult): void
	{
		$this->assertEquals(str_replace("_DS_", DIRECTORY_SEPARATOR, $expectedResult), Path::resolve($path));
	}

	/**
	 * Test resolve method
 *

	 * @param  string  $path  test path
	 *
	 * @return void
	 *
	 * @since   1.4.0
	 *
	 * @dataProvider  getResolveExceptionData
	 */
	public function testResolveThrowsExceptionIfRootIsLeft($path): void
	{
		$this->expectExceptionMessage("Path is outside of the defined root");
		$this->expectException(FilesystemException::class);
		Path::resolve($path);
	}

	/**
	 * Data provider for testResolve() method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getResolveData(): array
	{
		return array(
			array("/", "_DS_"),
			array("a", "a"),
			array("/test/", "_DS_test"),
			array("C:/", "C:"),
			array("/var/www/joomla", "_DS_var_DS_www_DS_joomla"),
			array("C:/iis/www/joomla", "C:_DS_iis_DS_www_DS_joomla"),
			array("var/www/joomla", "var_DS_www_DS_joomla"),
			array("./var/www/joomla", "var_DS_www_DS_joomla"),
			array("/var/www/foo/../joomla", "_DS_var_DS_www_DS_joomla"),
			array("C:/var/www/foo/../joomla", "C:_DS_var_DS_www_DS_joomla"),
			array("/var/www/../foo/../joomla", "_DS_var_DS_joomla"),
			array("C:/var/www/..foo../joomla", "C:_DS_var_DS_www_DS_..foo.._DS_joomla"),
			array("c:/var/www/..foo../joomla", "c:_DS_var_DS_www_DS_..foo.._DS_joomla"),
			array("/var/www///joomla", "_DS_var_DS_www_DS_joomla"),
			array("/var///www///joomla", "_DS_var_DS_www_DS_joomla"),
			array("C:/var///www///joomla", "C:_DS_var_DS_www_DS_joomla"),
			array("/var/\/../www///joomla", "_DS_www_DS_joomla"),
			array("C:/var///www///joomla", "C:_DS_var_DS_www_DS_joomla"),
			array("/var\\www///joomla", "_DS_var_DS_www_DS_joomla")
		);
	}

	/**
	 * Data provider for testResolve() method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getResolveExceptionData(): array
	{
		return array(
			array("../var/www/joomla"),
			array("/var/../../../www/joomla")
		);
	}
}
