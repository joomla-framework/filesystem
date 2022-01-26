<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php71;

use Joomla\Filesystem\File;

/**
 * Test class for Joomla\Filesystem\File.
 *
 * @since  1.0
 */
class FileTest extends FilesystemTestCase
{
	/**
	 * Provides the data to test the makeSafe method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestStripExt(): array
	{
		return array(
			array(
				'foobar.php',
				'foobar',
			),
			array(
				'foobar..php',
				'foobar.',
			),
			array(
				'foobar.php.',
				'foobar.php',
			),
		);
	}

	/**
	 * Test makeSafe method
	 *
	 * @param  string  $fileName        The name of the file with extension
	 * @param  string  $nameWithoutExt  Name without extension
	 *
	 * @return  void
	 *
	 * @dataProvider  dataTestStripExt
	 * @since         1.0
	 */
	public function testStripExt(string $fileName, string $nameWithoutExt): void
	{
		$this->assertEquals(
			File::stripExt($fileName),
			$nameWithoutExt,
			'File extension should be stripped'
		);
	}

	/**
	 * Provides the data to test the makeSafe method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestMakeSafe(): array
	{
		return array(
			array(
				'joomla.',
				array('#^\.#'),
				'joomla',
				'There should be no fullstop on the end of a filename',
			),
			array(
				'Test j00mla_5-1.html',
				array('#^\.#'),
				'Test j00mla_5-1.html',
				'Alphanumeric symbols, dots, dashes, spaces and underscores should not be filtered',
			),
			array(
				'Test j00mla_5-1.html',
				array('#^\.#', '/\s+/'),
				'Testj00mla_5-1.html',
				'Using strip chars parameter here to strip all spaces',
			),
			array(
				'joomla.php!.',
				array('#^\.#'),
				'joomla.php',
				'Non-alphanumeric symbols should be filtered to avoid disguising file extensions',
			),
			array(
				'joomla.php.!',
				array('#^\.#'),
				'joomla.php',
				'Non-alphanumeric symbols should be filtered to avoid disguising file extensions',
			),
			array(
				'.gitignore',
				array(),
				'.gitignore',
				'Files starting with a fullstop should be allowed when strip chars parameter is empty',
			),
		);
	}

	/**
	 * Test makeSafe method.
	 *
	 * @param  string  $name        The name of the file to test filtering of
	 * @param  array   $stripChars  Whether to filter spaces out the name or not
	 * @param  string  $expected    The expected safe file name
	 * @param  string  $message     The message to show on failure of test
	 *
	 * @return  void
	 *
	 * @covers        \Joomla\Filesystem\File::makeSafe
	 * @dataProvider  dataTestMakeSafe
	 * @since         1.0
	 */
	public function testMakeSafe(string $name, array $stripChars, string $expected, string $message): void
	{
		$this->assertEquals(File::makeSafe($name, $stripChars), $expected, $message);
	}

	/**
	 * Test copy method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopyWithPathArgPassed(): void
	{
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';
		$data       = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::copy($name, $copiedName, $this->testPath),
			'The file was not copied.'
		);

		$this->assertFileEquals(
			$this->testPath . '/' . $name,
			$this->testPath . '/' . $copiedName,
			'Content should remain intact after copy.'
		);
	}

	/**
	 * Test copy method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopyWithoutPathArgPassed(): void
	{
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';
		$data       = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::copy($this->testPath . '/' . $name, $this->testPath . '/' . $copiedName),
			'The file was not copied.'
		);

		$this->assertFileEquals(
			$this->testPath . '/' . $name,
			$this->testPath . '/' . $copiedName,
			'Content should remain intact after copy.'
		);
	}

	/**
	 * Test copy method using streams.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopyWithStreams(): void
	{
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';
		$data       = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::copy($name, $copiedName, $this->testPath, true),
			'The file was not copied.'
		);

		$this->assertFileEquals(
			$this->testPath . '/' . $name,
			$this->testPath . '/' . $copiedName,
			'Content should remain intact after copy.'
		);
	}

	/**
	 * Test makeCopy method for an exception
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testCopySrcDontExist(): void
	{
		$this->expectException(\UnexpectedValueException::class);
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';

		File::copy($name, $copiedName, $this->testPath);
	}

	/**
	 * Test delete method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDeleteForSingleFile(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::delete($this->testPath . '/' . $name),
			'The file was not deleted.'
		);
	}

	/**
	 * Test delete method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDeleteForArrayOfFiles(): void
	{
		$name1 = 'tempFile1';
		$name2 = 'tempFile2';
		$data  = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name1, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name2, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::delete(array($this->testPath . '/' . $name1, $this->testPath . '/' . $name2)),
			'The files were not deleted.'
		);
	}

	/**
	 * Tests the File::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveWithPathArgPassed(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';
		$data      = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::move($name, $movedName, $this->testPath),
			'The test file was not moved.'
		);
	}

	/**
	 * Tests the File::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveWithoutPathArgPassed(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';
		$data      = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::move($this->testPath . '/' . $name, $this->testPath . '/' . $movedName),
			'The test file was not moved.'
		);
	}

	/**
	 * Tests the File::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveWithStreams(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';
		$data      = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::move($name, $movedName, $this->testPath, true),
			'The test directory was not moved.'
		);
	}


	/**
	 * Test the File::move method where source file doesn't exist.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveSrcDontExist(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';

		$this->assertSame(
			'Cannot find source file.',
			File::move($name, $movedName, $this->testPath)
		);
	}

	/**
	 * Test write method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testWrite(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name,
			$data,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test write method when appending to a file.
	 *
	 * @return  void
	 *
	 * @since   1.5.0
	 *
	 */
	public function testWriteWithAppend(): void
	{
		$name = 'tempFile.txt';
		$data = 'Lorem ipsum dolor sit amet';
		$appendData = PHP_EOL . $data;

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $appendData, false, true),
			'The file was not appended.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name,
			$data . $appendData,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test write method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testWriteCreatesMissingDirectory(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($this->testPath . '/' . $name . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name . '/' . $name,
			$data,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test write method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testWriteWithStreams(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $data, true),
			'The file was not written.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name,
			$data,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test upload method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 *
	 * @backupGlobals enabled
	 */
	public function testUpload(): void
	{
		include_once dirname(__DIR__) . '/Stubs/PHPUploadStub.php';

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';
		$uploadedFileName = 'uploadedFileName';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$_FILES = array(
			'test' => array(
				'name'     => 'test.jpg',
				'tmp_name' => $this->testPath . '/' . $name,
			)
		);

		$this->assertTrue(
			File::upload($this->testPath . '/' . $name, $this->testPath . '/' . $uploadedFileName)
		);
	}

	/**
	 * Test upload method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 *
	 * @backupGlobals enabled
	 */
	public function testUploadWithStreams(): void
	{
		include_once dirname(__DIR__) . '/Stubs/PHPUploadStub.php';

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';
		$uploadedFileName = 'uploadedFileName';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$_FILES = array(
			'test' => array(
				'name'     => 'test.jpg',
				'tmp_name' => $this->testPath . '/' . $name,
			)
		);

		$this->assertTrue(
			File::upload($this->testPath . '/' . $name, $this->testPath . '/' . $uploadedFileName, true)
		);
	}

	/**
	 * Test upload method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 *
	 * @backupGlobals enabled
	 */
	public function testUploadToNestedDirectory(): void
	{
		include_once dirname(__DIR__) . '/Stubs/PHPUploadStub.php';

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';
		$uploadedFileName = 'uploadedFileName';

		if (!File::write($this->testPath . '/' . $name . '.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$_FILES = array(
			'test' => array(
				'name'     => 'test.jpg',
				'tmp_name' => $this->testPath . '/' . $name . '.txt',
			)
		);

		$this->assertTrue(
			File::upload($this->testPath . '/' . $name . '.txt', $this->testPath . '/' . $name . '/' . $uploadedFileName)
		);
	}
}
